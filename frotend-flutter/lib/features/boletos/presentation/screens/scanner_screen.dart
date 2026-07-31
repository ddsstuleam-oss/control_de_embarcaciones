import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '../../../viajes/providers/viaje_provider.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../auth/providers/auth_provider.dart';
import '../../../../core/widgets/web_centered.dart';

class ScannerScreen extends ConsumerStatefulWidget {
  const ScannerScreen({super.key});

  @override
  ConsumerState<ScannerScreen> createState() => _ScannerScreenState();
}

class _ScannerScreenState extends ConsumerState<ScannerScreen> {
  final MobileScannerController _controller = MobileScannerController();
  bool _procesando = false;
  bool _escaneado = false;

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  Future<void> _onDetect(BarcodeCapture capture) async {
    if (_procesando || _escaneado) return;

    final codigo = capture.barcodes.firstOrNull?.rawValue;
    if (codigo == null) return;

    setState(() {
      _procesando = true;
      _escaneado = true;
    });

    await _controller.stop();
    await _validar(codigo);
  }

  Future<void> _validar(String codigo) async {
    setState(() => _procesando = true);

    try {
      final result = await ref
          .read(viajeRepositoryProvider)
          .escanear(codigo.trim().toUpperCase());

      if (!mounted) return;

      final viajeId = (result['viaje'] as Map<String, dynamic>)['id'] as int;
      await context.push('/viajes/$viajeId');

      if (!mounted) return;
      setState(() => _escaneado = false);
      await _controller.start();
    } catch (e) {
      if (!mounted) return;
      _mostrarError(e.toString().replaceAll('Exception: ', ''));
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _ingresarManual() async {
    await _controller.stop();

    final codigo = await showDialog<String>(
      context: context,
      builder: (_) {
        final ctrl = TextEditingController();
        return AlertDialog(
          title: const Text(
            'Validar código manual',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w700,
              color: AppTheme.primary,
            ),
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text(
                'Ingresa el código que aparece debajo del QR en el boleto',
                style: TextStyle(
                  fontSize: 13,
                  color: AppTheme.textMuted,
                ),
              ),
              const SizedBox(height: 16),
              TextField(
                controller: ctrl,
                autofocus: true,
                textCapitalization: TextCapitalization.characters,
                decoration: const InputDecoration(
                  labelText: 'Código del boleto',
                  hintText: 'Ej: 01KPSRM0D62MP2HAXD0NE04358',
                  prefixIcon: Icon(Icons.qr_code),
                  border: OutlineInputBorder(),
                ),
              ),
            ],
          ),
          actions: [
            TextButton(
              onPressed: () {
                _controller.start();
                Navigator.pop(context);
              },
              child: const Text('Cancelar'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.pop(context, ctrl.text),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.primary,
              ),
              child: const Text('Validar'),
            ),
          ],
        );
      },
    );

    if (codigo != null && codigo.isNotEmpty) {
      setState(() => _escaneado = true);
      await _validar(codigo);
    } else {
      _controller.start();
    }
  }

  void _mostrarError(String mensaje) {
    showModalBottomSheet(
      context: context,
      isDismissible: false,
      enableDrag: false,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (_) => _ResultadoSheet(
        mensaje: mensaje,
        onNuevoEscaneo: () {
          Navigator.pop(context);
          setState(() => _escaneado = false);
          _controller.start();
        },
        onIngresarManual: () {
          Navigator.pop(context);
          setState(() => _escaneado = false);
          _ingresarManual();
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.black,
        foregroundColor: Colors.white,
        title: const Text('Validar boleto'),
        actions: [
          IconButton(
            icon: const Icon(Icons.flash_on),
            onPressed: () => _controller.toggleTorch(),
            tooltip: 'Linterna',
          ),
          IconButton(
            icon: const Icon(Icons.flip_camera_ios),
            onPressed: () => _controller.switchCamera(),
            tooltip: 'Cambiar cámara',
          ),
          // ← Agregar este botón
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Cerrar sesión',
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
              if (confirm == true && mounted) {
                await ref.read(authProvider.notifier).logout();
              }
            },
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 480,
        child: Stack(
          children: [
            // Cámara — Positioned.fill para que se ajuste al tamaño real
            // del Stack en vez de imponer el suyo. Sin esto, en web/PC el
            // Stack terminaba tan alto como pidiera la cámara (más que la
            // pantalla), empujando las instrucciones de abajo fuera de la
            // vista.
            Positioned.fill(
              child: MobileScanner(
                controller: _controller,
                onDetect: _onDetect,
              ),
            ),

            // Overlay — hecho con widgets (Container/Positioned), no con
            // CustomPaint/Canvas. Con el renderer HTML de Flutter Web,
            // Chrome/Edge no logran componer un Canvas por encima del
            // <video> de la cámara (limitación conocida del motor con
            // "platform views"), así que el recuadro dibujado con Canvas
            // quedaba invisible ahí, aunque en Brave y en CanvasKit sí se
            // veía. Con widgets normales (que se pintan como DOM real) se
            // apilan igual en todos los navegadores.
            const Positioned.fill(
              child: _ScannerOverlay(),
            ),

            // Instrucciones + botón manual
            Positioned(
              bottom: 40,
              left: 0,
              right: 0,
              child: Column(
                children: [
                  if (_procesando)
                    const CircularProgressIndicator(color: Colors.white)
                  else
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 24,
                        vertical: 12,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.black.withOpacity(0.6),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Text(
                        'Apunta la cámara al código QR del boleto',
                        style: TextStyle(color: Colors.white, fontSize: 14),
                        textAlign: TextAlign.center,
                      ),
                    ),

                  const SizedBox(height: 16),

                  // Botón ingreso manual
                  TextButton.icon(
                    onPressed: _procesando ? null : _ingresarManual,
                    icon: const Icon(
                      Icons.keyboard_alt_outlined,
                      color: Colors.white70,
                    ),
                    label: const Text(
                      'Ingresar código manualmente',
                      style: TextStyle(
                        color: Colors.white70,
                        fontSize: 13,
                        decoration: TextDecoration.underline,
                        decorationColor: Colors.white70,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// Overlay del scanner — construido con widgets normales (Container /
// Positioned), no con CustomPaint/Canvas. Ver comentario en el Stack de
// ScannerScreen: el Canvas no se compone por encima del <video> de la
// cámara en Chrome/Edge con el renderer HTML de Flutter Web.
class _ScannerOverlay extends StatelessWidget {
  const _ScannerOverlay();

  static const double _side = 250;
  static const double _radius = 12;
  static const double _cornerLength = 24;
  static const double _cornerThickness = 4;
  static const double _borderWidth = 3;

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final left = (constraints.maxWidth - _side) / 2;
        final top = (constraints.maxHeight - _side) / 2;
        final mask = Colors.black.withOpacity(0.5);

        return Stack(
          children: [
            // Franjas oscuras alrededor del recuadro central.
            Positioned(top: 0, left: 0, right: 0, height: top, child: Container(color: mask)),
            Positioned(bottom: 0, left: 0, right: 0, height: top, child: Container(color: mask)),
            Positioned(top: top, left: 0, width: left, height: _side, child: Container(color: mask)),
            Positioned(top: top, right: 0, width: left, height: _side, child: Container(color: mask)),

            // Marco del recuadro.
            Positioned(
              left: left,
              top: top,
              width: _side,
              height: _side,
              child: DecoratedBox(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(_radius),
                  border: Border.all(color: AppTheme.accent, width: _borderWidth),
                ),
              ),
            ),

            // Esquinas blancas.
            for (final corner in _cornerRects(left, top))
              Positioned(
                left: corner.left,
                top: corner.top,
                width: corner.width,
                height: corner.height,
                child: const DecoratedBox(
                  decoration: BoxDecoration(color: Colors.white),
                ),
              ),
          ],
        );
      },
    );
  }

  List<Rect> _cornerRects(double left, double top) {
    final right = left + _side;
    final bottom = top + _side;

    return [
      // Superior izquierda
      Rect.fromLTWH(left, top, _cornerLength, _cornerThickness),
      Rect.fromLTWH(left, top, _cornerThickness, _cornerLength),
      // Superior derecha
      Rect.fromLTWH(right - _cornerLength, top, _cornerLength, _cornerThickness),
      Rect.fromLTWH(right - _cornerThickness, top, _cornerThickness, _cornerLength),
      // Inferior izquierda
      Rect.fromLTWH(left, bottom - _cornerThickness, _cornerLength, _cornerThickness),
      Rect.fromLTWH(left, bottom - _cornerLength, _cornerThickness, _cornerLength),
      // Inferior derecha
      Rect.fromLTWH(right - _cornerLength, bottom - _cornerThickness, _cornerLength, _cornerThickness),
      Rect.fromLTWH(right - _cornerThickness, bottom - _cornerLength, _cornerThickness, _cornerLength),
    ];
  }
}

// Sheet de resultado (código inválido / reserva no disponible)
class _ResultadoSheet extends StatelessWidget {
  final String mensaje;
  final VoidCallback onNuevoEscaneo;
  final VoidCallback onIngresarManual;

  const _ResultadoSheet({
    required this.mensaje,
    required this.onNuevoEscaneo,
    required this.onIngresarManual,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      child: Container(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Handle
            Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 20),

            // Indicador
            Container(
              width: 72,
              height: 72,
              decoration: BoxDecoration(
                color: AppTheme.error.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.cancel,
                color: AppTheme.error,
                size: 44,
              ),
            ),
            const SizedBox(height: 12),

            const Text(
              'No se pudo abrir el viaje',
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.w700,
                color: AppTheme.error,
              ),
            ),
            const SizedBox(height: 8),

            Text(
              mensaje,
              style: const TextStyle(
                color: AppTheme.textMuted,
                fontSize: 14,
              ),
              textAlign: TextAlign.center,
            ),

            const SizedBox(height: 24),

            // Botones
            ElevatedButton.icon(
              onPressed: onNuevoEscaneo,
              icon: const Icon(Icons.qr_code_scanner),
              label: const Text('Escanear otro boleto'),
              style: ElevatedButton.styleFrom(
                minimumSize: const Size(double.infinity, 48),
              ),
            ),
            const SizedBox(height: 8),
            OutlinedButton.icon(
              onPressed: onIngresarManual,
              icon: const Icon(Icons.keyboard_alt_outlined),
              label: const Text('Ingresar código manual'),
              style: OutlinedButton.styleFrom(
                minimumSize: const Size(double.infinity, 48),
                foregroundColor: AppTheme.primary,
                side: const BorderSide(color: AppTheme.primary),
              ),
            ),
            const SizedBox(height: 8),
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Cerrar'),
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );
  }
}
