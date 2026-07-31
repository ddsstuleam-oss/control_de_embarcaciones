import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../theme/app_theme.dart';

/// Estado de error reutilizable para pantallas que cargan datos vía HTTP.
///
/// Si el error es un 401 (token invalidado — sesión expirada, cambio de
/// contraseña, u otro dispositivo cerrando la sesión), muestra un mensaje
/// claro con acceso directo al login en vez de volcar la excepción cruda de
/// Dio en pantalla.
class ErrorStateWidget extends StatelessWidget {
  final Object        error;
  final VoidCallback? onRetry;

  const ErrorStateWidget({super.key, required this.error, this.onRetry});

  bool get _esSesionExpirada =>
      error is DioException && (error as DioException).response?.statusCode == 401;

  @override
  Widget build(BuildContext context) {
    if (_esSesionExpirada) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.lock_clock_outlined, size: 48, color: AppTheme.textMuted),
              const SizedBox(height: 16),
              const Text(
                'Tu sesión expiró',
                style: TextStyle(
                  fontSize:   18,
                  fontWeight: FontWeight.w700,
                  color:      AppTheme.primary,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 8),
              const Text(
                'Vuelve a iniciar sesión para continuar.',
                style: TextStyle(fontSize: 14, color: AppTheme.textMuted),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () => context.go('/welcome'),
                child: const Text('Iniciar sesión'),
              ),
            ],
          ),
        ),
      );
    }

    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.error_outline, size: 48, color: AppTheme.error),
            const SizedBox(height: 12),
            const Text(
              'No se pudo cargar la información',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 6),
            Text(
              '$error',
              style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
              textAlign: TextAlign.center,
            ),
            if (onRetry != null) ...[
              const SizedBox(height: 16),
              ElevatedButton(onPressed: onRetry, child: const Text('Reintentar')),
            ],
          ],
        ),
      ),
    );
  }
}
