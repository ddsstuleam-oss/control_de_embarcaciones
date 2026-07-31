import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../providers/auth_provider.dart';

class VerifyEmailScreen extends ConsumerStatefulWidget {
  const VerifyEmailScreen({super.key});

  @override
  ConsumerState<VerifyEmailScreen> createState() => _VerifyEmailScreenState();
}

class _VerifyEmailScreenState extends ConsumerState<VerifyEmailScreen> {
  final List<TextEditingController> _controllers =
      List.generate(6, (_) => TextEditingController());
  final List<FocusNode> _focusNodes = List.generate(6, (_) => FocusNode());

  bool _isLoading = false;
  bool _isResending = false;
  String? _error;

  @override
  void dispose() {
    for (final c in _controllers) c.dispose();
    for (final f in _focusNodes) f.dispose();
    super.dispose();
  }

  String get _code => _controllers.map((c) => c.text).join();

  Future<void> _verificar() async {
    if (_code.length != 6) {
      setState(() => _error = 'Ingresa los 6 dígitos del código');
      return;
    }

    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final response = await ApiClient().dio.post(
        ApiEndpoints.verifyEmail,
        data: {'code': _code},
      );

      if (response.statusCode == 200) {
        if (!mounted) return;

        final userData = response.data['user'] as Map<String, dynamic>?;
        await ref.read(authProvider.notifier).setVerified(userData);

        if (!mounted) return;

        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('¡Correo verificado correctamente!'),
            backgroundColor: AppTheme.success,
          ),
        );

        context.go('/home');
      } else {
        setState(() {
          _error = response.data['error'] ?? 'Código incorrecto';
        });
      }
    } catch (e) {
      setState(() => _error = 'Error al verificar. Intenta de nuevo.');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _reenviar() async {
    setState(() {
      _isResending = true;
      _error = null;
    });

    try {
      await ApiClient().dio.post(ApiEndpoints.resendVerification);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Código reenviado a tu correo'),
            backgroundColor: AppTheme.success,
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Error al reenviar código'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _isResending = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final email = ref.watch(authProvider).user?['email'] ?? '';

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: WebCentered(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                const SizedBox(height: 40),

                // Ícono
                Container(
                  width: 90,
                  height: 90,
                  decoration: BoxDecoration(
                    color: AppTheme.primary.withOpacity(0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.mark_email_unread_outlined,
                    color: AppTheme.primary,
                    size: 46,
                  ),
                ),

                const SizedBox(height: 24),

                const Text(
                  'Verifica tu correo',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    color: AppTheme.primary,
                  ),
                ),

                const SizedBox(height: 8),

                const Text(
                  'Hemos enviado un código de 6 dígitos a',
                  style: TextStyle(
                    fontSize: 14,
                    color: AppTheme.textMuted,
                  ),
                  textAlign: TextAlign.center,
                ),

                const SizedBox(height: 4),

                Text(
                  email,
                  style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.primary,
                  ),
                ),

                const SizedBox(height: 32),

                // Error
                if (_error != null) ...[
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppTheme.error.withOpacity(0.08),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: AppTheme.error.withOpacity(0.3),
                      ),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.error_outline,
                            color: AppTheme.error, size: 18),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            _error!,
                            style: const TextStyle(
                              color: AppTheme.error,
                              fontSize: 13,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                ],

                // Campos de código
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
                    children: [
                      const Text(
                        'Ingresa el código',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w600,
                          color: AppTheme.primary,
                        ),
                      ),
                      const SizedBox(height: 20),

                      // 6 campos
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: List.generate(
                            6,
                            (i) => SizedBox(
                                  width: 44,
                                  child: TextField(
                                    controller: _controllers[i],
                                    focusNode: _focusNodes[i],
                                    keyboardType: TextInputType.number,
                                    textAlign: TextAlign.center,
                                    maxLength: 1,
                                    style: const TextStyle(
                                      fontSize: 22,
                                      fontWeight: FontWeight.w800,
                                      color: AppTheme.primary,
                                    ),
                                    decoration: InputDecoration(
                                      counterText: '',
                                      filled: true,
                                      fillColor: Colors.grey[100],
                                      border: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(10),
                                        borderSide: BorderSide(
                                            color: Colors.grey[300]!),
                                      ),
                                      focusedBorder: OutlineInputBorder(
                                        borderRadius: BorderRadius.circular(10),
                                        borderSide: const BorderSide(
                                          color: AppTheme.primary,
                                          width: 2,
                                        ),
                                      ),
                                      contentPadding:
                                          const EdgeInsets.symmetric(
                                              vertical: 14),
                                    ),
                                    onChanged: (v) {
                                      if (v.isNotEmpty && i < 5) {
                                        _focusNodes[i + 1].requestFocus();
                                      }
                                      if (v.isEmpty && i > 0) {
                                        _focusNodes[i - 1].requestFocus();
                                      }
                                      setState(() => _error = null);
                                      // Auto verificar cuando se completen 6
                                      if (_code.length == 6) _verificar();
                                    },
                                  ),
                                )),
                      ),

                      const SizedBox(height: 24),

                      // Botón verificar
                      ElevatedButton(
                        onPressed: _isLoading ? null : _verificar,
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
                            : const Text('Verificar correo'),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 24),

                // Reenviar
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Text(
                      '¿No recibiste el código? ',
                      style: TextStyle(color: AppTheme.textMuted),
                    ),
                    TextButton(
                      onPressed: _isResending ? null : _reenviar,
                      child: _isResending
                          ? const SizedBox(
                              width: 16,
                              height: 16,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                color: AppTheme.primary,
                              ),
                            )
                          : const Text(
                              'Reenviar',
                              style: TextStyle(
                                color: AppTheme.primary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                    ),
                  ],
                ),

                const SizedBox(height: 16),

                // Cerrar sesión
                TextButton(
                  onPressed: () async {
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
                    if (confirm == true && context.mounted) {
                      await ref.read(authProvider.notifier).logout();
                    }
                  },
                  child: const Text(
                    'Cerrar sesión',
                    style: TextStyle(color: AppTheme.error),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
