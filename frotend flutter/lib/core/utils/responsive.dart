import 'package:flutter/material.dart';

/// Breakpoints simples para adaptar columnas/tamaños según el ancho real de
/// pantalla, sin depender de un paquete externo. Pensado para casos puntuales
/// (grillas, layouts de 2 columnas) — la mayoría de pantallas ya se adaptan
/// solas gracias a Column/ListView/Expanded.
class Responsive {
  static const double tablet  = 600;
  static const double desktop = 1000;

  static bool isTablet(BuildContext context) =>
      MediaQuery.of(context).size.width >= tablet;

  static bool isDesktop(BuildContext context) =>
      MediaQuery.of(context).size.width >= desktop;

  /// Número de columnas para una grilla que normalmente se ve en `base`
  /// columnas en celular — crece en tablets/pantallas grandes.
  static int gridColumns(BuildContext context, {int base = 2}) {
    final width = MediaQuery.of(context).size.width;
    if (width >= desktop) return base + 2;
    if (width >= tablet) return base + 1;
    return base;
  }
}
