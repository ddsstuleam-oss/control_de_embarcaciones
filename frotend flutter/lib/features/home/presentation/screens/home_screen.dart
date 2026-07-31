import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/boleto_utils.dart';
import '../../../../core/utils/responsive.dart';
import '../../../../features/auth/providers/auth_provider.dart';
import '../../../../features/embarcaciones/providers/embarcacion_provider.dart';
import '../../../../core/widgets/web_centered.dart';

final proximaReservaProvider =
    FutureProvider.autoDispose<Map<String, dynamic>?>((ref) async {
  final response = await ApiClient().dio.get(
    ApiEndpoints.reservas,
    queryParameters: {'per_page': 5},
  );
  if (response.statusCode == 200) {
    final List reservas = response.data['data'] ?? [];
    final hoy = DateTime.now();

    final proximas = reservas
        .cast<Map<String, dynamic>>()
        .where((r) =>
            r['estado'] == 'confirmada' &&
            DateTime.tryParse(r['fecha'] ?? '')?.isAfter(
                  hoy.subtract(const Duration(days: 1)),
                ) ==
                true)
        .toList();

    if (proximas.isEmpty) return null;
    proximas.sort((a, b) =>
        DateTime.parse(a['fecha']).compareTo(DateTime.parse(b['fecha'])));
    return proximas.first;
  }
  throw Exception('Error');
});

// ← Cambiar a ConsumerStatefulWidget
class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  bool _passwordChecked = false;

  String _saludo() {
    final hora = DateTime.now().hour;
    if (hora < 12) return 'Buenos días';
    if (hora < 18) return 'Buenas tardes';
    return 'Buenas noches';
  }

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _checkPassword();
    });
  }

  void _checkPassword() {
    if (_passwordChecked) return;
    _passwordChecked = true;

    final authState = ref.read(authProvider);
    final dias = (authState.user?['dias_para_vencer'] as num?)?.toInt() ?? 90;

    if (dias <= 5 && mounted) {
      context.push('/cambiar-password?dias=$dias');
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final proximaAsync = ref.watch(proximaReservaProvider);
    final fecha = DateFormat('yyyy-MM-dd').format(DateTime.now());
    final disponibilidad = ref.watch(disponibilidadProvider(fecha));
    final nombre = authState.user?['nombre'] as String? ?? '';
    final primerNombre = nombre.split(' ').first;

    final proximaReservaSection = Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Próxima reserva',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: AppTheme.primary,
              ),
            ),
            TextButton(
              onPressed: () => context.push('/reservas'),
              child: const Text('Ver todas'),
            ),
          ],
        ),
        const SizedBox(height: 8),
        proximaAsync.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (_, __) => const SizedBox.shrink(),
          data: (reserva) => reserva == null
              ? Container(
                  padding: const EdgeInsets.all(20),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.05),
                        blurRadius: 8,
                      ),
                    ],
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: AppTheme.primary.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Icon(
                          Icons.directions_boat_outlined,
                          color: AppTheme.primary,
                          size: 28,
                        ),
                      ),
                      const SizedBox(width: 16),
                      const Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Sin reservas próximas',
                              style: TextStyle(
                                fontWeight: FontWeight.w600,
                                color: AppTheme.primary,
                              ),
                            ),
                            SizedBox(height: 4),
                            Text(
                              '¡Haz tu primera reserva ahora!',
                              style: TextStyle(
                                fontSize: 12,
                                color: AppTheme.textMuted,
                              ),
                            ),
                          ],
                        ),
                      ),
                      ElevatedButton(
                        onPressed: () => context.push('/embarcaciones'),
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 8,
                          ),
                          minimumSize: Size.zero,
                        ),
                        child: const Text('Reservar',
                            style: TextStyle(fontSize: 12)),
                      ),
                    ],
                  ),
                )
              : _ProximaReservaCard(reserva: reserva),
        ),
      ],
    );

    final disponiblesHoySection = Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Disponibles hoy',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w700,
                color: AppTheme.primary,
              ),
            ),
            TextButton(
              onPressed: () => context.push('/embarcaciones'),
              child: const Text('Ver todas'),
            ),
          ],
        ),
        const SizedBox(height: 8),
        disponibilidad.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (_, __) => const SizedBox.shrink(),
          data: (embarcaciones) {
            final disponibles =
                embarcaciones.where((e) => e['disponible'] == true).toList();

            if (disponibles.isEmpty) {
              return Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Center(
                  child: Text(
                    'No hay embarcaciones disponibles hoy',
                    style: TextStyle(color: AppTheme.textMuted),
                  ),
                ),
              );
            }

            return Column(
              children: disponibles.take(3).map((e) {
                final em = e;
                final disp = (em['disponibles'] as num?)?.toInt() ?? 0;
                final cap = (em['capacidad'] as num?)?.toInt() ?? 0;
                final porcentaje =
                    (em['porcentaje'] as num?)?.toDouble() ?? 0.0;

                return Container(
                  margin: const EdgeInsets.only(bottom: 10),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.05),
                        blurRadius: 8,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: AppTheme.primary.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: const Icon(
                              Icons.directions_boat,
                              color: AppTheme.primary,
                              size: 18,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  em['nombre'] ?? '',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w700,
                                    fontSize: 14,
                                    color: AppTheme.primary,
                                  ),
                                ),
                                Text(
                                  '$disp de $cap disponibles',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: AppTheme.textMuted,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          ElevatedButton(
                            onPressed: () => context.push(
                              '/reservas/crear?embarcacion_id=${em['id']}&fecha=$fecha',
                            ),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppTheme.accent,
                              padding: const EdgeInsets.symmetric(
                                horizontal: 12,
                                vertical: 6,
                              ),
                              minimumSize: Size.zero,
                            ),
                            child: const Text('Reservar',
                                style: TextStyle(fontSize: 12)),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),
                      ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: LinearProgressIndicator(
                          value: porcentaje / 100,
                          backgroundColor: Colors.grey[200],
                          valueColor: AlwaysStoppedAnimation(
                            porcentaje > 80 ? AppTheme.error : AppTheme.success,
                          ),
                          minHeight: 5,
                        ),
                      ),
                    ],
                  ),
                );
              }).toList(),
            );
          },
        ),
      ],
    );

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: WebCentered(
        maxWidth: 900,
        child: RefreshIndicator(
          onRefresh: () async {
            ref.invalidate(proximaReservaProvider);
            ref.invalidate(disponibilidadProvider(fecha));
          },
          child: CustomScrollView(
            slivers: [
              // ── AppBar personalizado ─────────────────────────
              SliverAppBar(
                expandedHeight: 160,
                floating: false,
                pinned: true,
                backgroundColor: AppTheme.primary,
                actions: [
                  IconButton(
                    icon: const Icon(Icons.notifications_outlined,
                        color: Colors.white),
                    onPressed: () {},
                  ),
                  IconButton(
                    icon: const Icon(Icons.person_outline, color: Colors.white),
                    onPressed: () => context.push('/perfil'),
                  ),
                  IconButton(
                    icon: const Icon(Icons.logout, color: Colors.white),
                    tooltip: 'Cerrar sesión',
                    onPressed: () async {
                      final confirmar = await showDialog<bool>(
                        context: context,
                        builder: (_) => AlertDialog(
                          title: const Text('Cerrar sesión'),
                          content:
                              const Text('¿Estás seguro de cerrar sesión?'),
                          actions: [
                            TextButton(
                              onPressed: () => Navigator.pop(context, false),
                              child: const Text('Cancelar'),
                            ),
                            TextButton(
                              onPressed: () => Navigator.pop(context, true),
                              child: const Text(
                                'Cerrar sesión',
                                style: TextStyle(color: AppTheme.accent),
                              ),
                            ),
                          ],
                        ),
                      );
                      if (confirmar == true && context.mounted) {
                        await ref.read(authProvider.notifier).logout();
                      }
                    },
                  ),
                ],
                flexibleSpace: FlexibleSpaceBar(
                  background: Container(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: [
                          AppTheme.primary,
                          Color(0xFF0A3D62),
                        ],
                      ),
                    ),
                    child: Stack(
                      children: [
                        Positioned(
                          top: -20,
                          right: -20,
                          child: Container(
                            width: 120,
                            height: 120,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: Colors.white.withOpacity(0.05),
                            ),
                          ),
                        ),
                        Positioned(
                          bottom: 10,
                          left: -30,
                          child: Container(
                            width: 100,
                            height: 100,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: AppTheme.accent.withOpacity(0.1),
                            ),
                          ),
                        ),
                        Positioned(
                          bottom: 20,
                          left: 20,
                          right: 20,
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                '${_saludo()},',
                                style: const TextStyle(
                                  color: Colors.white70,
                                  fontSize: 14,
                                ),
                              ),
                              Text(
                                primerNombre,
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 26,
                                  fontWeight: FontWeight.w800,
                                ),
                              ),
                              Text(
                                DateFormat('EEEE, d MMMM yyyy', 'es')
                                    .format(DateTime.now()),
                                style: const TextStyle(
                                  color: Colors.white54,
                                  fontSize: 12,
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

              SliverPadding(
                padding: const EdgeInsets.all(16),
                sliver: SliverList(
                  delegate: SliverChildListDelegate([
                    // ── Accesos rápidos ──────────────────────────
                    Row(
                      children: [
                        _AccesoRapido(
                          icon: Icons.directions_boat_rounded,
                          label: 'Reservar',
                          color: AppTheme.primary,
                          onTap: () => context.push('/embarcaciones'),
                        ),
                        const SizedBox(width: 12),
                        _AccesoRapido(
                          icon: Icons.book_online_outlined,
                          label: 'Mis reservas',
                          color: AppTheme.info,
                          onTap: () => context.push('/reservas'),
                        ),
                        const SizedBox(width: 12),
                        _AccesoRapido(
                          icon: Icons.person_outline,
                          label: 'Perfil',
                          color: AppTheme.warning,
                          onTap: () => context.push('/perfil'),
                        ),
                      ],
                    ),

                    const SizedBox(height: 20),

                    // En escritorio web hay espacio de sobra: "Próxima
                    // reserva" y "Disponibles hoy" van una al lado de la
                    // otra en vez de apiladas. Ojo: sin
                    // crossAxisAlignment.stretch — un Row estirado dentro
                    // de un scroll de alto infinito revienta el layout.
                    if (kIsWeb && Responsive.isDesktop(context))
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(child: proximaReservaSection),
                          const SizedBox(width: 16),
                          Expanded(child: disponiblesHoySection),
                        ],
                      )
                    else ...[
                      proximaReservaSection,
                      const SizedBox(height: 20),
                      disponiblesHoySection,
                    ],

                    const SizedBox(height: 32),
                  ]),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ── Widgets ───────────────────────────────────────────────────────────────────

class _AccesoRapido extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _AccesoRapido({
    required this.icon,
    required this.label,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.05),
                blurRadius: 6,
              ),
            ],
          ),
          child: Column(
            children: [
              Icon(icon, color: color, size: 24),
              const SizedBox(height: 4),
              Text(
                label,
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                  color: color,
                ),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ProximaReservaCard extends StatelessWidget {
  final Map<String, dynamic> reserva;
  const _ProximaReservaCard({required this.reserva});

  @override
  Widget build(BuildContext context) {
    final embarcacion = reserva['embarcacion'] as Map<String, dynamic>?;
    final fecha = reserva['fecha'] as String? ?? '';
    final horaInicio = reserva['hora_inicio'] as String? ?? '';
    final horaFin = reserva['hora_fin'] as String? ?? '';
    final boleto = reserva['boleto'] as Map<String, dynamic>?;
    final personas = (reserva['total_personas'] as num?)?.toInt() ?? 0;
    final fechaDate = DateTime.tryParse(fecha);
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final fechaSoloFecha = fechaDate != null
        ? DateTime(fechaDate.year, fechaDate.month, fechaDate.day)
        : null;
    final diasRestantes =
        fechaSoloFecha != null ? fechaSoloFecha.difference(today).inDays : 0;

    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [AppTheme.primary, Color(0xFF0A3D62)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: AppTheme.primary.withOpacity(0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.directions_boat_rounded,
                  color: Colors.white, size: 20),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  embarcacion?['nombre'] ?? '',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 4,
                ),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  diasRestantes == 0
                      ? '¡Hoy!'
                      : diasRestantes == 1
                          ? 'Mañana'
                          : 'En $diasRestantes días',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          const Divider(color: Colors.white24),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: _ReservaInfo(
                  icon: Icons.calendar_today,
                  label: 'Fecha',
                  value: fechaDate != null
                      ? DateFormat('dd/MM/yyyy').format(fechaDate)
                      : fecha,
                ),
              ),
              if (horaInicio.isNotEmpty)
                Expanded(
                  child: _ReservaInfo(
                    icon: Icons.access_time,
                    label: 'Hora',
                    value: horaFin.isNotEmpty
                        ? '$horaInicio - $horaFin'
                        : horaInicio,
                  ),
                ),
              Expanded(
                child: _ReservaInfo(
                  icon: Icons.people_outline,
                  label: 'Pasajeros',
                  value: '$personas',
                ),
              ),
              if (boleto != null)
                Expanded(
                  child: _ReservaInfo(
                    icon: Icons.qr_code,
                    label: 'Boleto',
                    value: estadoBoletoLabel(boleto['estado'] ?? ''),
                  ),
                ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: () => context.push('/reservas/${reserva['id']}'),
                  icon: const Icon(Icons.info_outline,
                      color: Colors.white, size: 16),
                  label: const Text('Ver detalle',
                      style: TextStyle(color: Colors.white)),
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: Colors.white54),
                    padding: const EdgeInsets.symmetric(vertical: 8),
                  ),
                ),
              ),
              if (boleto != null) ...[
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: () => context.push('/boletos/${boleto['id']}'),
                    icon: const Icon(Icons.qr_code, size: 16),
                    label: const Text('Ver QR'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.accent,
                      padding: const EdgeInsets.symmetric(vertical: 8),
                    ),
                  ),
                ),
              ],
            ],
          ),
        ],
      ),
    );
  }
}

class _ReservaInfo extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _ReservaInfo({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 12, color: Colors.white54),
            const SizedBox(width: 4),
            Text(label,
                style: const TextStyle(
                  color: Colors.white54,
                  fontSize: 10,
                )),
          ],
        ),
        const SizedBox(height: 2),
        Text(
          value,
          style: const TextStyle(
            color: Colors.white,
            fontSize: 13,
            fontWeight: FontWeight.w700,
          ),
        ),
      ],
    );
  }
}
