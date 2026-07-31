import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../providers/auth_provider.dart';
import '../../../../core/theme/app_theme.dart';

class RegisterScreen extends ConsumerStatefulWidget {
  const RegisterScreen({super.key});

  @override
  ConsumerState<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends ConsumerState<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _cedulaCtrl = TextEditingController();
  final _nameCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();
  final _telefonoCtrl = TextEditingController();
  final _passwordCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();
  bool _obscure = true;
  bool _obscureConfirm = true;

  @override
  void dispose() {
    _cedulaCtrl.dispose();
    _nameCtrl.dispose();
    _emailCtrl.dispose();
    _telefonoCtrl.dispose();
    _passwordCtrl.dispose();
    _confirmCtrl.dispose();
    super.dispose();
  }

  Future<void> _register() async {
    if (!_formKey.currentState!.validate()) return;

    final success = await ref.read(authProvider.notifier).register(
          cedula: _cedulaCtrl.text.trim(),
          name: _nameCtrl.text.trim(),
          email: _emailCtrl.text.trim(),
          telefono: _telefonoCtrl.text.trim(),
          password: _passwordCtrl.text,
          passwordConfirmation: _confirmCtrl.text,
        );

    if (!mounted) return;

    if (success) {
      final authState = ref.read(authProvider);
      if (authState.requiresVerification) {
        context.go('/verify-email');
      }
    } else {
      final error = ref.read(authProvider).error ?? 'Error al registrarse';
      final yaRegistrado = error.toLowerCase().contains('registrada') ||
          error.toLowerCase().contains('registrado');

      if (yaRegistrado) {
        showDialog(
          context: context,
          builder: (_) => AlertDialog(
            title: const Text('Cuenta existente'),
            content: Text(error),
            actions: [
              TextButton(
                onPressed: () {
                  Navigator.pop(context);
                  context.go('/login');
                },
                child: const Text('Iniciar sesión'),
              ),
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('Cancelar'),
              ),
            ],
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(error),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isLoading = ref.watch(authProvider).isLoading;

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Crear cuenta'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.pop(),
        ),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Center(
            child: ConstrainedBox(
              constraints:
                  BoxConstraints(maxWidth: kIsWeb ? 440 : double.infinity),
              child: Form(
                key: _formKey,
                child: Column(
                  children: [
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.all(24),
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
                          // Cédula
                          TextFormField(
                            controller: _cedulaCtrl,
                            keyboardType: TextInputType.number,
                            maxLength: 10,
                            decoration: const InputDecoration(
                              labelText: 'Cédula',
                              prefixIcon: Icon(Icons.badge_outlined),
                              counterText: '',
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty)
                                return 'Ingresa tu cédula';
                              if (v.length != 10)
                                return 'La cédula debe tener 10 dígitos';
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Nombre
                          TextFormField(
                            controller: _nameCtrl,
                            keyboardType: TextInputType.name,
                            decoration: const InputDecoration(
                              labelText: 'Nombre completo',
                              prefixIcon: Icon(Icons.person_outline),
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty)
                                return 'Ingresa tu nombre';
                              if (v.length < 3) return 'Nombre muy corto';
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Email
                          TextFormField(
                            controller: _emailCtrl,
                            keyboardType: TextInputType.emailAddress,
                            decoration: const InputDecoration(
                              labelText: 'Correo electrónico',
                              prefixIcon: Icon(Icons.email_outlined),
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty)
                                return 'Ingresa tu correo';
                              if (!v.contains('@')) return 'Correo inválido';
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Teléfono
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
                          const SizedBox(height: 16),

                          // Contraseña
                          TextFormField(
                            controller: _passwordCtrl,
                            obscureText: _obscure,
                            decoration: InputDecoration(
                              labelText: 'Contraseña',
                              prefixIcon: const Icon(Icons.lock_outline),
                              suffixIcon: IconButton(
                                icon: Icon(
                                  _obscure
                                      ? Icons.visibility_off
                                      : Icons.visibility,
                                ),
                                onPressed: () =>
                                    setState(() => _obscure = !_obscure),
                              ),
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty)
                                return 'Ingresa una contraseña';
                              if (v.length < 8) return 'Mínimo 8 caracteres';
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Confirmar contraseña
                          TextFormField(
                            controller: _confirmCtrl,
                            obscureText: _obscureConfirm,
                            decoration: InputDecoration(
                              labelText: 'Confirmar contraseña',
                              prefixIcon: const Icon(Icons.lock_outline),
                              suffixIcon: IconButton(
                                icon: Icon(
                                  _obscureConfirm
                                      ? Icons.visibility_off
                                      : Icons.visibility,
                                ),
                                onPressed: () => setState(
                                    () => _obscureConfirm = !_obscureConfirm),
                              ),
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty)
                                return 'Confirma tu contraseña';
                              if (v != _passwordCtrl.text)
                                return 'Las contraseñas no coinciden';
                              return null;
                            },
                          ),
                          const SizedBox(height: 24),

                          // Botón
                          ElevatedButton(
                            onPressed: isLoading ? null : _register,
                            child: isLoading
                                ? const SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      color: Colors.white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : const Text('Crear cuenta'),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Text(
                          '¿Ya tienes cuenta? ',
                          style: TextStyle(color: AppTheme.textMuted),
                        ),
                        TextButton(
                          onPressed: () => context.push('/login'),
                          child: const Text(
                            'Inicia sesión',
                            style: TextStyle(
                              color: AppTheme.primary,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
