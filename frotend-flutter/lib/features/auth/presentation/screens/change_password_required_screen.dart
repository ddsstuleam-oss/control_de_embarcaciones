import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../providers/auth_provider.dart';

class ChangePasswordRequiredScreen extends ConsumerStatefulWidget {
  final int diasRestantes;
  const ChangePasswordRequiredScreen({
    super.key,
    required this.diasRestantes,
  });

  @override
  ConsumerState<ChangePasswordRequiredScreen> createState() =>
      _ChangePasswordRequiredScreenState();
}

class _ChangePasswordRequiredScreenState
    extends ConsumerState<ChangePasswordRequiredScreen> {
  final _formKey = GlobalKey<FormState>();
  final _currentCtrl = TextEditingController();
  final _newCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();

  bool _obscureCurrent = true;
  bool _obscureNew = true;
  bool _obscureConfirm = true;
  bool _isLoading = false;
  String? _error;

  bool get _estaVencida => widget.diasRestantes <= 0;

  @override
  void dispose() {
    _currentCtrl.dispose();
    _newCtrl.dispose();
    _confirmCtrl.dispose();
    super.dispose();
  }

  Future<void> _cambiar() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      await ApiClient().dio.post(
        ApiEndpoints.updatePassword,
        data: {
          'current_password': _currentCtrl.text,
          'new_password': _newCtrl.text,
          'new_password_confirmation': _confirmCtrl.text,
        },
      );

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Contraseña actualizada correctamente'),
          backgroundColor: AppTheme.success,
        ),
      );

      // Logout para relogin con nueva contraseña
      await ref.read(authProvider.notifier).logout();
    } catch (e) {
      setState(() {
        _isLoading = false;
        _error = 'Contraseña actual incorrecta. Intenta de nuevo.';
      });
    }
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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
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

                  // ── Ícono de advertencia ─────────────────────
                  Container(
                    width: 90,
                    height: 90,
                    decoration: BoxDecoration(
                      color: (_estaVencida ? AppTheme.error : AppTheme.warning)
                          .withOpacity(0.1),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(
                      _estaVencida
                          ? Icons.lock_outlined
                          : Icons.lock_clock_outlined,
                      size: 46,
                      color: _estaVencida ? AppTheme.error : AppTheme.warning,
                    ),
                  ),

                  const SizedBox(height: 24),

                  // ── Título ───────────────────────────────────
                  Text(
                    _estaVencida
                        ? 'Contraseña vencida'
                        : 'Contraseña por vencer',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.w800,
                      color: _estaVencida ? AppTheme.error : AppTheme.warning,
                    ),
                    textAlign: TextAlign.center,
                  ),

                  const SizedBox(height: 8),

                  Text(
                    _estaVencida
                        ? 'Tu contraseña ha vencido.\nDebes cambiarla para continuar.'
                        : 'Tu contraseña vence en ${widget.diasRestantes} día${widget.diasRestantes > 1 ? 's' : ''}.\nTe recomendamos cambiarla ahora.',
                    style: const TextStyle(
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
                          'Cambiar contraseña',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.primary,
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'La nueva contraseña debe tener mínimo 8 caracteres',
                          style: TextStyle(
                            fontSize: 12,
                            color: AppTheme.textMuted,
                          ),
                        ),
                        const SizedBox(height: 20),

                        // Contraseña actual
                        TextFormField(
                          controller: _currentCtrl,
                          obscureText: _obscureCurrent,
                          decoration: InputDecoration(
                            labelText: 'Contraseña actual',
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              icon: Icon(_obscureCurrent
                                  ? Icons.visibility_off
                                  : Icons.visibility),
                              onPressed: () => setState(
                                  () => _obscureCurrent = !_obscureCurrent),
                            ),
                          ),
                          validator: (v) =>
                              v == null || v.isEmpty ? 'Requerido' : null,
                        ),
                        const SizedBox(height: 14),

                        // Nueva contraseña
                        TextFormField(
                          controller: _newCtrl,
                          obscureText: _obscureNew,
                          decoration: InputDecoration(
                            labelText: 'Nueva contraseña',
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              icon: Icon(_obscureNew
                                  ? Icons.visibility_off
                                  : Icons.visibility),
                              onPressed: () =>
                                  setState(() => _obscureNew = !_obscureNew),
                            ),
                          ),
                          validator: (v) {
                            if (v == null || v.isEmpty) return 'Requerido';
                            if (v.length < 8) return 'Mínimo 8 caracteres';
                            if (v == _currentCtrl.text)
                              return 'Debe ser diferente a la actual';
                            return null;
                          },
                        ),
                        const SizedBox(height: 14),

                        // Confirmar contraseña
                        TextFormField(
                          controller: _confirmCtrl,
                          obscureText: _obscureConfirm,
                          decoration: InputDecoration(
                            labelText: 'Confirmar nueva contraseña',
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              icon: Icon(_obscureConfirm
                                  ? Icons.visibility_off
                                  : Icons.visibility),
                              onPressed: () => setState(
                                  () => _obscureConfirm = !_obscureConfirm),
                            ),
                          ),
                          validator: (v) {
                            if (v == null || v.isEmpty) return 'Requerido';
                            if (v != _newCtrl.text)
                              return 'Las contraseñas no coinciden';
                            return null;
                          },
                        ),

                        const SizedBox(height: 20),

                        // Botón cambiar
                        ElevatedButton(
                          onPressed: _isLoading ? null : _cambiar,
                          style: ElevatedButton.styleFrom(
                            minimumSize: const Size(double.infinity, 50),
                            backgroundColor: _estaVencida
                                ? AppTheme.error
                                : AppTheme.primary,
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
                              : const Text('Cambiar contraseña'),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 16),

                  // Solo mostrar "saltar" si no está vencida
                  if (!_estaVencida) ...[
                    TextButton(
                      onPressed: () => context.go(
                        _rolHome(ref.read(authProvider).rol),
                      ),
                      child: const Text(
                        'Recordármelo después',
                        style: TextStyle(color: AppTheme.textMuted),
                      ),
                    ),
                  ] else ...[
                    TextButton(
                      onPressed: _cerrarSesion,
                      child: const Text(
                        'Cerrar sesión',
                        style: TextStyle(color: AppTheme.error),
                      ),
                    ),
                  ],

                  const SizedBox(height: 32),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  String _rolHome(String? rol) {
    switch (rol) {
      case 'admin':
        return '/admin/dashboard';
      case 'operador':
        return '/operador';
      default:
        return '/home';
    }
  }
}
