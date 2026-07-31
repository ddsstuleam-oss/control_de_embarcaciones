import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

class EmptyStateWidget extends StatelessWidget {
  final IconData icon;
  final String   titulo;
  final String   mensaje;
  final String?  botonLabel;
  final VoidCallback? onBoton;

  const EmptyStateWidget({
    super.key,
    required this.icon,
    required this.titulo,
    required this.mensaje,
    this.botonLabel,
    this.onBoton,
  });

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [

            // Icono animado
            TweenAnimationBuilder<double>(
              tween:    Tween(begin: 0.8, end: 1.0),
              duration: const Duration(milliseconds: 600),
              curve:    Curves.elasticOut,
              builder:  (_, value, child) => Transform.scale(
                scale: value,
                child: child,
              ),
              child: Container(
                width:  110,
                height: 110,
                decoration: BoxDecoration(
                  color:  AppTheme.primary.withOpacity(0.08),
                  shape:  BoxShape.circle,
                ),
                child: Icon(icon, size: 54, color: AppTheme.primary.withOpacity(0.5)),
              ),
            ),

            const SizedBox(height: 24),

            Text(
              titulo,
              style: const TextStyle(
                fontSize:   18,
                fontWeight: FontWeight.w700,
                color:      AppTheme.primary,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),
            Text(
              mensaje,
              style: const TextStyle(
                fontSize: 14,
                color:    AppTheme.textMuted,
                height:   1.5,
              ),
              textAlign: TextAlign.center,
            ),

            if (botonLabel != null && onBoton != null) ...[
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: onBoton,
                icon:  const Icon(Icons.add),
                label: Text(botonLabel!),
                style: ElevatedButton.styleFrom(
                  minimumSize: const Size(200, 48),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}