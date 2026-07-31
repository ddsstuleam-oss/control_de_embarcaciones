import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/responsive.dart';

class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({super.key});

  @override
  Widget build(BuildContext context) {

    return Scaffold(
      body: Container(
        width:  double.infinity,
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin:  Alignment.topCenter,
            end:    Alignment.bottomCenter,
            colors: [
              Color(0xFF0D1B2A),
              Color(0xFF1A1A2E),
              Color(0xFF0A3D62),
              Color(0xFF1A1A2E),
            ],
            stops: [0.0, 0.3, 0.6, 1.0],
          ),
        ),
        child: Stack(
          children: [

            // Círculos decorativos
            Positioned(
              top:   -80,
              right: -80,
              child: Container(
                width:  220,
                height: 220,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppTheme.accent.withOpacity(0.12),
                ),
              ),
            ),
            Positioned(
              top:  150,
              left: -50,
              child: Container(
                width:  130,
                height: 130,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withOpacity(0.04),
                ),
              ),
            ),
            Positioned(
              bottom: 100,
              right:  -40,
              child: Container(
                width:  160,
                height: 160,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: AppTheme.accent.withOpacity(0.07),
                ),
              ),
            ),

            // Contenido principal
            SafeArea(
              child: kIsWeb ? _buildWeb(context) : _buildMobile(context),
            ),
          ],
        ),
      ),
    );
  }

  List<Widget> _opciones(BuildContext context) => [
        _OpcionCard(
          icon:     Icons.login_rounded,
          label:    'INGRESAR',
          sublabel: 'Accede a tu cuenta',
          color:    AppTheme.accent,
          onTap:    () => context.push('/login'),
        ),
        _OpcionCard(
          icon:     Icons.person_add_rounded,
          label:    'REGISTRARSE',
          sublabel: 'Crea tu cuenta',
          color:    const Color(0xFF00B4D8),
          onTap:    () => context.push('/register'),
        ),
        _OpcionCard(
          icon:     Icons.directions_rounded,
          label:    'CÓMO LLEGAR',
          sublabel: 'Ver ubicación',
          color:    const Color(0xFF00C853),
          onTap:    () => context.push('/como-llegar'),
        ),
        _OpcionCard(
          icon:     Icons.info_outline_rounded,
          label:    'INFORMACIÓN',
          sublabel: 'Sobre el sistema',
          color:    const Color(0xFFFF9800),
          onTap:    () => _mostrarInfo(context),
        ),
      ];

  Widget _header({bool compact = false}) {
    final logoAltura  = compact ? 40.0 : 55.0;
    final iconoLado    = compact ? 66.0 : 90.0;
    final iconoTamano  = compact ? 34.0 : 46.0;
    final tituloTamano = compact ? 24.0 : 32.0;

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [

        // Logo ULEAM + Icono barco lado a lado
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Logo ULEAM
            Container(
              padding: const EdgeInsets.symmetric(
                horizontal: 12,
                vertical:    6,
              ),
              decoration: BoxDecoration(
                color:        Colors.white,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color:      Colors.black.withOpacity(0.25),
                    blurRadius: 10,
                    offset:     const Offset(0, 3),
                  ),
                ],
              ),
              child: Image.asset(
                'assets/images/logo-uleam-nuevo.png',
                height: logoAltura,
              ),
            ),

            const SizedBox(width: 16),

            // Icono barco
            Container(
              width:  iconoLado,
              height: iconoLado,
              decoration: BoxDecoration(
                color:  Colors.white.withOpacity(0.1),
                shape:  BoxShape.circle,
                border: Border.all(
                  color: Colors.white.withOpacity(0.25),
                  width: 2,
                ),
              ),
              child: Icon(
                Icons.directions_boat_rounded,
                color: Colors.white,
                size:  iconoTamano,
              ),
            ),
          ],
        ),

        SizedBox(height: compact ? 12 : 16),

        const Text(
          'FCVT — ULEAM',
          style: TextStyle(
            color:         Colors.white60,
            fontSize:      12,
            letterSpacing: 3,
            fontWeight:    FontWeight.w400,
          ),
        ),

        SizedBox(height: compact ? 6 : 8),

        Text(
          'Control de\nEmbarcaciones',
          textAlign: TextAlign.center,
          style: TextStyle(
            color:      Colors.white,
            fontSize:   tituloTamano,
            fontWeight: FontWeight.w800,
            height:     1.2,
          ),
        ),

        SizedBox(height: compact ? 10 : 14),

        // Badge facultad
        Container(
          margin: const EdgeInsets.symmetric(horizontal: 32),
          padding: const EdgeInsets.symmetric(
            horizontal: 14,
            vertical:    7,
          ),
          decoration: BoxDecoration(
            color:        Colors.white.withOpacity(0.08),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(
              color: Colors.white.withOpacity(0.15),
            ),
          ),
          child: const Text(
            'Facultad de Ciencias de la Vida y Tecnologías',
            textAlign: TextAlign.center,
            style: TextStyle(
              color:    Colors.white54,
              fontSize: 11,
              height:   1.4,
            ),
          ),
        ),
      ],
    );
  }

  Widget _divisor() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 40),
      child: Row(
        children: [
          Expanded(
            child: Container(
              height: 1,
              color:  Colors.white.withOpacity(0.1),
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12),
            child: Container(
              width:  6,
              height: 6,
              decoration: BoxDecoration(
                color:  AppTheme.accent.withOpacity(0.7),
                shape:  BoxShape.circle,
              ),
            ),
          ),
          Expanded(
            child: Container(
              height: 1,
              color:  Colors.white.withOpacity(0.1),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMobile(BuildContext context) {
    return Column(
      children: [

        // ── Header ───────────────────────────────────────
        Expanded(
          flex: 4,
          child: Center(child: _header()),
        ),

        _divisor(),

        const SizedBox(height: 24),

        // ── Grid de opciones ─────────────────────────────
        Expanded(
          flex: 5,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Column(
              children: [
                Expanded(
                  child: GridView.count(
                    crossAxisCount:   Responsive.gridColumns(context),
                    crossAxisSpacing: 14,
                    mainAxisSpacing:  14,
                    physics: const NeverScrollableScrollPhysics(),
                    children: _opciones(context),
                  ),
                ),

                // Versión
                const SizedBox(height: 8),
                const Text(
                  'v1.0.0 · ULEAM 2026',
                  style: TextStyle(
                    color:    Colors.white24,
                    fontSize: 11,
                  ),
                ),
                const SizedBox(height: 8),
              ],
            ),
          ),
        ),
      ],
    );
  }

  // En web el Column a pantalla completa con Expanded/flex estiraba las
  // tarjetas de la grilla a la altura de toda la ventana (se veían enormes
  // y deformadas en monitores anchos) — acá el contenido se centra en un
  // ancho fijo tipo tarjeta, con la grilla usando su alto natural en vez
  // de forzarse a llenar el viewport.
  Widget _buildWeb(BuildContext context) {
    return Center(
      child: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(vertical: 20),
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 400),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              _header(compact: true),
              const SizedBox(height: 18),
              _divisor(),
              const SizedBox(height: 18),
              GridView.count(
                crossAxisCount:   2,
                crossAxisSpacing: 12,
                mainAxisSpacing:  12,
                shrinkWrap:       true,
                childAspectRatio: 1.6,
                physics: const NeverScrollableScrollPhysics(),
                children: _opciones(context),
              ),
              const SizedBox(height: 16),
              const Text(
                'v1.0.0 · ULEAM 2026',
                style: TextStyle(
                  color:    Colors.white24,
                  fontSize: 11,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _mostrarInfo(BuildContext context) {
    showModalBottomSheet(
      context:            context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (_) => SafeArea(
        child: SingleChildScrollView(
          child: Container(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width:  40,
                  height: 4,
                  decoration: BoxDecoration(
                    color:        Colors.grey[300],
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
                const SizedBox(height: 20),
                const Icon(
                  Icons.directions_boat_rounded,
                  color: AppTheme.primary,
                  size:  48,
                ),
                const SizedBox(height: 12),
                const Text(
                  'Sistema de Control de Embarcaciones',
                  style: TextStyle(
                    fontSize:   18,
                    fontWeight: FontWeight.w700,
                    color:      AppTheme.primary,
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 8),
                const Text(
                  'Facultad de Ciencias de la Vida y Tecnologías\nUniversidad Laica Eloy Alfaro de Manabí',
                  style: TextStyle(
                    fontSize: 13,
                    color:    AppTheme.textMuted,
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 16),
                const Divider(),
                const SizedBox(height: 12),
                _InfoRow(label: 'Versión',    value: '1.0.0'),
                _InfoRow(label: 'Año',        value: '2026'),
                _InfoRow(label: 'Plataforma', value: 'Android'),
                _InfoRow(label: 'Tecnología', value: 'Flutter + Laravel'),
                _InfoRow(label: 'Desarrollador', value: 'Jean Mosquera y Luis Cornejo'),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: () => Navigator.pop(context),
                  style: ElevatedButton.styleFrom(
                    minimumSize: const Size(double.infinity, 46),
                  ),
                  child: const Text('Cerrar'),
                ),
                const SizedBox(height: 8),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final String label;
  final String value;
  const _InfoRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        children: [
          SizedBox(
            width: 100,
            child: Text(
              label,
              style: const TextStyle(
                color:    AppTheme.textMuted,
                fontSize: 13,
              ),
            ),
          ),
          Text(
            value,
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              fontSize:   13,
              color:      AppTheme.primary,
            ),
          ),
        ],
      ),
    );
  }
}

class _OpcionCard extends StatelessWidget {
  final IconData     icon;
  final String       label;
  final String       sublabel;
  final Color        color;
  final VoidCallback onTap;

  const _OpcionCard({
    required this.icon,
    required this.label,
    required this.sublabel,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap:        onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        decoration: BoxDecoration(
          color:        Colors.white.withOpacity(0.07),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: color.withOpacity(0.35),
            width: 1.5,
          ),
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width:  54,
              height: 54,
              decoration: BoxDecoration(
                color: color.withOpacity(0.15),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: color, size: 26),
            ),
            const SizedBox(height: 10),
            Text(
              label,
              style: const TextStyle(
                color:         Colors.white,
                fontSize:      12,
                fontWeight:    FontWeight.w800,
                letterSpacing: 1,
              ),
            ),
            const SizedBox(height: 3),
            Text(
              sublabel,
              style: const TextStyle(
                color:    Colors.white38,
                fontSize: 10,
              ),
            ),
          ],
        ),
      ),
    );
  }
}