import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';

/// Centra [child] con un ancho máximo cuando corre en web, para que
/// formularios/listas pensados para una pantalla de celular no se estiren
/// de punta a punta en un monitor ancho. En móvil no hace nada — devuelve
/// [child] tal cual, sin restricciones extra.
class WebCentered extends StatelessWidget {
  final Widget child;
  final double maxWidth;

  const WebCentered({super.key, required this.child, this.maxWidth = 440});

  @override
  Widget build(BuildContext context) {
    if (!kIsWeb) return child;
    return Center(
      child: ConstrainedBox(
        constraints: BoxConstraints(maxWidth: maxWidth),
        child: child,
      ),
    );
  }
}
