import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/theme/app_theme.dart';

class ComoLlegarScreen extends StatelessWidget {
  const ComoLlegarScreen({super.key});

// ignore: unused_field
  static const double _lat = -0.9547;
// ignore: unused_field
  static const double _lng = -80.7234;
  static const String _direccion =
      'Muelle Pesquero Artesanal, Jaramijo, Manabí, Ecuador';
  static const String _referencia =
      'Facultad de Ciencias de la Vida y Tecnologías — ULEAM';
  static const String _mapsUrl = 'https://maps.app.goo.gl/c85SqPw77NUGMHdZ6';

// Y actualiza el método _abrirGoogleMaps:
  void _abrirGoogleMaps() async {
    final uri = Uri.parse(_mapsUrl);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  void _copiarDireccion(BuildContext context) {
    Clipboard.setData(const ClipboardData(text: _direccion));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Dirección copiada al portapapeles'),
        backgroundColor: AppTheme.success,
        duration: Duration(seconds: 2),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Cómo llegar'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => context.pop(),
        ),
      ),
      body: SingleChildScrollView(
        child: Center(
          child: ConstrainedBox(
            constraints:
                BoxConstraints(maxWidth: kIsWeb ? 640 : double.infinity),
            child: Column(
              children: [
                // ── Mapa estático con iframe-style visual ─────────────
                Container(
                  height: 280,
                  width: double.infinity,
                  decoration: const BoxDecoration(
                    color: Color(0xFFE8E8E8),
                  ),
                  child: LayoutBuilder(
                    builder: (context, constraints) => Stack(
                      children: [
                        // Fondo del mapa simulado
                        Container(
                          decoration: const BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                              colors: [
                                Color(0xFFD4E6C3),
                                Color(0xFFB8D4A8),
                              ],
                            ),
                          ),
                        ),

                        // Patrón de calles simulado
                        CustomPaint(
                          painter: _MapPainter(),
                          child: const SizedBox.expand(),
                        ),

                        // Marcador central
                        Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: AppTheme.accent,
                                  shape: BoxShape.circle,
                                  boxShadow: [
                                    BoxShadow(
                                      color: AppTheme.accent.withOpacity(0.4),
                                      blurRadius: 12,
                                      spreadRadius: 4,
                                    ),
                                  ],
                                ),
                                child: const Icon(
                                  Icons.location_on,
                                  color: Colors.white,
                                  size: 32,
                                ),
                              ),
                              const SizedBox(height: 4),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 10,
                                  vertical: 4,
                                ),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(8),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.15),
                                      blurRadius: 8,
                                    ),
                                  ],
                                ),
                                child: const Text(
                                  'FCVT — ULEAM',
                                  style: TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.w700,
                                    color: AppTheme.primary,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),

                        // Botón abrir en Maps
                        Positioned(
                          bottom: 12,
                          right: 12,
                          child: ConstrainedBox(
                            constraints: BoxConstraints(
                              maxWidth: constraints.maxWidth - 24,
                            ),
                            child: ElevatedButton.icon(
                              onPressed: () => _abrirGoogleMaps(),
                              icon: const Icon(Icons.open_in_new, size: 16),
                              label: const Text(
                                'Abrir en Maps',
                                overflow: TextOverflow.ellipsis,
                              ),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.white,
                                foregroundColor: AppTheme.primary,
                                elevation: 2,
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 12,
                                  vertical: 8,
                                ),
                                textStyle: const TextStyle(fontSize: 12),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),

                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: [
                      // ── Card dirección ───────────────────────────
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.06),
                              blurRadius: 10,
                              offset: const Offset(0, 3),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                    color: AppTheme.accent.withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: const Icon(
                                    Icons.location_on,
                                    color: AppTheme.accent,
                                    size: 24,
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const Text(
                                        'Dirección',
                                        style: TextStyle(
                                          fontSize: 11,
                                          color: AppTheme.textMuted,
                                        ),
                                      ),
                                      const Text(
                                        _direccion,
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.w600,
                                          color: AppTheme.primary,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.copy,
                                      color: AppTheme.primary, size: 20),
                                  onPressed: () => _copiarDireccion(context),
                                  tooltip: 'Copiar dirección',
                                ),
                              ],
                            ),
                            const Divider(height: 20),
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                    color: AppTheme.primary.withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: const Icon(
                                    Icons.school_outlined,
                                    color: AppTheme.primary,
                                    size: 24,
                                  ),
                                ),
                                const SizedBox(width: 12),
                                const Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'Institución',
                                        style: TextStyle(
                                          fontSize: 11,
                                          color: AppTheme.textMuted,
                                        ),
                                      ),
                                      Text(
                                        _referencia,
                                        style: TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600,
                                          color: AppTheme.primary,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            const Divider(height: 20),
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(10),
                                  decoration: BoxDecoration(
                                    color: AppTheme.success.withOpacity(0.1),
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  child: const Icon(
                                    Icons.access_time,
                                    color: AppTheme.success,
                                    size: 24,
                                  ),
                                ),
                                const SizedBox(width: 12),
                                const Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'Horario de atención',
                                        style: TextStyle(
                                          fontSize: 11,
                                          color: AppTheme.textMuted,
                                        ),
                                      ),
                                      Text(
                                        'Lunes a Viernes: 08:00 — 17:00',
                                        style: TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600,
                                          color: AppTheme.primary,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 16),

                      // ── Cómo llegar ──────────────────────────────
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withOpacity(0.06),
                              blurRadius: 10,
                              offset: const Offset(0, 3),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Opciones de transporte',
                              style: TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.primary,
                              ),
                            ),
                            const SizedBox(height: 12),
                            _TransporteItem(
                              icon: Icons.directions_car,
                              color: AppTheme.primary,
                              titulo: 'En vehículo',
                              descripcion:
                                  'Toma la Av. Circunvalación hacia el norte, conecta con la Vía Puerto - Aeropuerto y sigue por la Vía Manta - Jaramijó hasta bajar al puerto por la Calle del Pacífico (aprox. 24 min).',
                            ),
                            const SizedBox(height: 12),
                            _TransporteItem(
                              icon: Icons.directions_bus,
                              color: AppTheme.info,
                              titulo: 'En bus',
                              descripcion:
                                  'Toma un bus urbano hacia el Malecón de Manta y allí haz transbordo a un bus intercantonal que vaya a Jaramijó.',
                            ),
                            const SizedBox(height: 12),
                            _TransporteItem(
                              icon: Icons.local_taxi,
                              color: const Color(0xFFFF9800),
                              titulo: 'En taxi / Uber',
                              descripcion:
                                  'Solicita el viaje indicando como destino directo el "Puerto Pesquero Jaramijó" en la Calle del Pacífico.',
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 16),

                      // ── Botón Google Maps ────────────────────────
                      ElevatedButton.icon(
                        onPressed: _abrirGoogleMaps,
                        icon: const Icon(Icons.map_outlined),
                        label: const Text('Ver en Google Maps'),
                        style: ElevatedButton.styleFrom(
                          minimumSize: const Size(double.infinity, 52),
                          backgroundColor: const Color(0xFF4285F4),
                        ),
                      ),

                      const SizedBox(height: 32),
                    ],
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

class _TransporteItem extends StatelessWidget {
  final IconData icon;
  final Color color;
  final String titulo;
  final String descripcion;

  const _TransporteItem({
    required this.icon,
    required this.color,
    required this.titulo,
    required this.descripcion,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: color.withOpacity(0.1),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, color: color, size: 20),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                titulo,
                style: const TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w700,
                  color: AppTheme.primary,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                descripcion,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppTheme.textMuted,
                  height: 1.4,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

// Pintor del mapa simulado
class _MapPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = Colors.white.withOpacity(0.6)
      ..strokeWidth = 8
      ..style = PaintingStyle.stroke;

    // Calles horizontales
    for (int i = 1; i < 5; i++) {
      canvas.drawLine(
        Offset(0, size.height * i / 5),
        Offset(size.width, size.height * i / 5),
        paint,
      );
    }

    // Calles verticales
    for (int i = 1; i < 5; i++) {
      canvas.drawLine(
        Offset(size.width * i / 5, 0),
        Offset(size.width * i / 5, size.height),
        paint,
      );
    }

    // Agua / mar (parte inferior)
    final waterPaint = Paint()
      ..color = const Color(0xFF90CAF9).withOpacity(0.4)
      ..style = PaintingStyle.fill;

    canvas.drawRect(
      Rect.fromLTWH(0, size.height * 0.7, size.width, size.height * 0.3),
      waterPaint,
    );
  }

  @override
  bool shouldRepaint(_) => false;
}
