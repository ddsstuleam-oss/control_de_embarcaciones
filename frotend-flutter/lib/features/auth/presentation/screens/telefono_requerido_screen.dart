import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../perfil/providers/perfil_provider.dart';
import '../../providers/auth_provider.dart';

/// Gate obligatorio que se muestra antes de crear una reserva si la cuenta
/// no tiene un teléfono de contacto guardado (necesario para emergencias).
/// A diferencia del cambio de contraseña, no tiene opción de "saltar" ni de
/// cerrar sesión: el teléfono es indispensable para reservar.
class TelefonoRequeridoScreen extends ConsumerStatefulWidget {
  const TelefonoRequeridoScreen({super.key});

  @override
  ConsumerState<TelefonoRequeridoScreen> createState() =>
      _TelefonoRequeridoScreenState();
}

class _TelefonoRequeridoScreenState
    extends ConsumerState<TelefonoRequeridoScreen> {
  final _formKey = GlobalKey<FormState>();
  final _telefonoCtrl = TextEditingController();
  bool _isLoading = false;
  String? _error;

  @override
  void dispose() {
    _telefonoCtrl.dispose();
    super.dispose();
  }

  Future<void> _guardar() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final data = await ref.read(perfilRepositoryProvider).actualizarPerfil(
            telefono: _telefonoCtrl.text.trim(),
          );

      final usuario = data['usuario'] as Map<String, dynamic>?;
      if (usuario != null) {
        await ref.read(authProvider.notifier).actualizarUsuario(usuario);
      }

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Teléfono guardado correctamente'),
          backgroundColor: AppTheme.success,
        ),
      );

      Navigator.of(context).pop();
    } catch (e) {
      setState(() {
        _isLoading = false;
        _error = e.toString().replaceAll('Exception: ', '');
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      child: Scaffold(
        backgroundColor: AppTheme.background,
        body: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: WebCentered(
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    const SizedBox(height: 32),

                    // ── Ícono ────────────────────────────────────
                    Container(
                      width: 90,
                      height: 90,
                      decoration: BoxDecoration(
                        color: AppTheme.warning.withOpacity(0.1),
                        shape: BoxShape.circle,
                      ),
                      child: const Icon(
                        Icons.phone_outlined,
                        size: 46,
                        color: AppTheme.warning,
                      ),
                    ),

                    const SizedBox(height: 24),

                    // ── Título ───────────────────────────────────
                    const Text(
                      'Falta tu teléfono',
                      style: TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.w800,
                        color: AppTheme.warning,
                      ),
                      textAlign: TextAlign.center,
                    ),

                    const SizedBox(height: 8),

                    const Text(
                      'Necesitamos un número de contacto antes de que '
                      'puedas reservar, para poder ubicarte en caso de '
                      'una emergencia durante el viaje.',
                      style: TextStyle(
                        fontSize: 14,
                        color: AppTheme.textMuted,
                        height: 1.5,
                      ),
                      textAlign: TextAlign.center,
                    ),

                    const SizedBox(height: 32),

                    // ── Error banner ─────────────────────────────
                    if (_error != null) ...[
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: AppTheme.error.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: AppTheme.error.withOpacity(0.4),
                          ),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.error_outline,
                                color: AppTheme.error, size: 20),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Text(
                                _error!,
                                style: const TextStyle(
                                  color: AppTheme.error,
                                  fontSize: 13,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                    ],

                    // ── Formulario ───────────────────────────────
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.06),
                            blurRadius: 16,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Teléfono de contacto',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w700,
                              color: AppTheme.primary,
                            ),
                          ),
                          const SizedBox(height: 4),
                          const Text(
                            'Entre 7 y 10 dígitos',
                            style: TextStyle(
                              fontSize: 12,
                              color: AppTheme.textMuted,
                            ),
                          ),
                          const SizedBox(height: 20),

                          TextFormField(
                            controller: _telefonoCtrl,
                            keyboardType: TextInputType.phone,
                            maxLength: 10,
                            decoration: const InputDecoration(
                              labelText: 'Teléfono',
                              prefixIcon: Icon(Icons.phone_outlined),
                              counterText: '',
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty) {
                                return 'Ingresa tu teléfono';
                              }
                              if (!RegExp(r'^\d{7,10}$').hasMatch(v)) {
                                return 'Debe tener entre 7 y 10 dígitos';
                              }
                              return null;
                            },
                          ),

                          const SizedBox(height: 20),

                          ElevatedButton(
                            onPressed: _isLoading ? null : _guardar,
                            style: ElevatedButton.styleFrom(
                              minimumSize: const Size(double.infinity, 50),
                            ),
                            child: _isLoading
                                ? const SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      color: Colors.white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : const Text('Guardar teléfono'),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 16),

                    TextButton(
                      onPressed: _cerrarSesion,
                      child: const Text(
                        'Cerrar sesión',
                        style: TextStyle(color: AppTheme.error),
                      ),
                    ),

                    const SizedBox(height: 32),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Future<void> _cerrarSesion() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Cerrar sesión'),
        content: const Text('¿Estás seguro de cerrar sesión?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text(
              'Cerrar sesión',
              style: TextStyle(color: AppTheme.error),
            ),
          ),
        ],
      ),
    );
    if (confirm == true && mounted) {
      await ref.read(authProvider.notifier).logout();
    }
  }
}
