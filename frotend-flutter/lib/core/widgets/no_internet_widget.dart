import 'package:flutter/material.dart';
import '../../../../core/theme/app_theme.dart';

class NoInternetWidget extends StatelessWidget {
  final VoidCallback onRetry;

  const NoInternetWidget({super.key, required this.onRetry});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width:  100,
              height: 100,
              decoration: BoxDecoration(
                color:  AppTheme.primary.withOpacity(0.1),
                shape:  BoxShape.circle,
              ),
              child: const Icon(
                Icons.wifi_off_rounded,
                size:  52,
                color: AppTheme.primary,
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'Sin conexión a internet',
              style: TextStyle(
                fontSize:   20,
                fontWeight: FontWeight.w700,
                color:      AppTheme.primary,
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Verifica tu conexión e intenta de nuevo',
              style: TextStyle(
                fontSize: 14,
                color:    AppTheme.textMuted,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: onRetry,
              icon:  const Icon(Icons.refresh),
              label: const Text('Reintentar'),
              style: ElevatedButton.styleFrom(
                minimumSize: const Size(160, 48),
              ),
            ),
          ],
        ),
      ),
    );
  }
}