import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../../core/widgets/password_strength_meter.dart';

class ForgotPasswordScreen extends ConsumerStatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  ConsumerState<ForgotPasswordScreen> createState() =>
      _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends ConsumerState<ForgotPasswordScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailCtrl = TextEditingController();
  final _tokenCtrl = TextEditingController();
  final _passCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();

  bool _isLoading = false;
  bool _tokenSent = false;
  bool _obscure = true;

  Future<void> _sendEmail() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);

    try {
      await ApiClient().dio.post(
        ApiEndpoints.forgotPassword,
        data: {'email': _emailCtrl.text.trim()},
      );
      setState(() => _tokenSent = true);
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Correo no registrado'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    } finally {
      setState(() => _isLoading = false);
    }
  }

  Future<void> _resetPassword() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isLoading = true);

    try {
      await ApiClient().dio.post(
        ApiEndpoints.resetPassword,
        data: {
          'email': _emailCtrl.text.trim(),
          'token': _tokenCtrl.text.trim(),
          'password': _passCtrl.text,
          'password_confirmation': _confirmCtrl.text,
        },
      );
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Contraseña actualizada correctamente'),
            backgroundColor: AppTheme.success,
          ),
        );
        context.go('/login');
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Token inválido o expirado'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(title: const Text('Recuperar contraseña')),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: WebCentered(
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
                        Text(
                          _tokenSent
                              ? 'Ingresa el código'
                              : 'Ingresa tu correo',
                          style:
                              Theme.of(context).textTheme.titleMedium?.copyWith(
                                    fontWeight: FontWeight.w700,
                                    color: AppTheme.primary,
                                  ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          _tokenSent
                              ? 'Revisa tu correo y escribe el código de 6 dígitos'
                              : 'Te enviaremos un código de recuperación',
                          style:
                              Theme.of(context).textTheme.bodySmall?.copyWith(
                                    color: AppTheme.textMuted,
                                  ),
                        ),
                        const SizedBox(height: 24),

                        // Email siempre visible
                        TextFormField(
                          controller: _emailCtrl,
                          keyboardType: TextInputType.emailAddress,
                          enabled: !_tokenSent,
                          decoration: const InputDecoration(
                            labelText: 'Correo electrónico',
                            prefixIcon: Icon(Icons.email_outlined),
                          ),
                          validator: (v) {
                            if (v == null || v.isEmpty) {
                              return 'Ingresa tu correo';
                            }
                            if (!v.contains('@')) return 'Correo inválido';
                            return null;
                          },
                        ),

                        if (_tokenSent) ...[
                          const SizedBox(height: 16),

                          // Token
                          TextFormField(
                            controller: _tokenCtrl,
                            decoration: const InputDecoration(
                              labelText: 'Código de verificación',
                              prefixIcon: Icon(Icons.pin_outlined),
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty)
                                return 'Ingresa el código';
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Nueva contraseña
                          TextFormField(
                            controller: _passCtrl,
                            obscureText: _obscure,
                            onChanged: (_) => setState(() {}),
                            decoration: InputDecoration(
                              labelText: 'Nueva contraseña',
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
                              if (v == null || v.isEmpty) {
                                return 'Ingresa la nueva contraseña';
                              }
                              if (v.length < 8) return 'Mínimo 8 caracteres';
                              if (!RegExp(r'[a-z]').hasMatch(v) ||
                                  !RegExp(r'[A-Z]').hasMatch(v)) {
                                return 'Combina mayúsculas y minúsculas';
                              }
                              if (!RegExp(r'[0-9]').hasMatch(v)) {
                                return 'Incluye al menos un número';
                              }
                              return null;
                            },
                          ),
                          PasswordStrengthMeter(password: _passCtrl.text),
                          const SizedBox(height: 16),

                          // Confirmar
                          TextFormField(
                            controller: _confirmCtrl,
                            obscureText: true,
                            decoration: const InputDecoration(
                              labelText: 'Confirmar contraseña',
                              prefixIcon: Icon(Icons.lock_outline),
                            ),
                            validator: (v) {
                              if (v == null || v.isEmpty)
                                return 'Confirma la contraseña';
                              if (v != _passCtrl.text)
                                return 'Las contraseñas no coinciden';
                              return null;
                            },
                          ),
                        ],

                        const SizedBox(height: 24),

                        ElevatedButton(
                          onPressed: _isLoading
                              ? null
                              : (_tokenSent ? _resetPassword : _sendEmail),
                          child: _isLoading
                              ? const SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    color: Colors.white,
                                    strokeWidth: 2,
                                  ),
                                )
                              : Text(
                                  _tokenSent
                                      ? 'Cambiar contraseña'
                                      : 'Enviar código',
                                ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
