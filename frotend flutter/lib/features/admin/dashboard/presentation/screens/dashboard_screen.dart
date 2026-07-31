import 'package:fl_chart/fl_chart.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/theme/app_theme.dart';
import '../../../../../core/api/api_client.dart';
import '../../../../../core/api/api_endpoints.dart';
import '../../../../../core/utils/responsive.dart';
import '../../../../../core/widgets/admin_nav.dart';
import '../../../../../core/widgets/admin_page_scaffold.dart';
import '../../../../../core/widgets/error_state_widget.dart';
import '../../../../../features/auth/providers/auth_provider.dart';

final dashboardProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.adminDashboard);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar dashboard');
});

final viajesActivosProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.viajesActivos);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar viajes en curso');
});

// ← ConsumerStatefulWidget para check de contraseña
class DashboardScreen extends ConsumerStatefulWidget {
  const DashboardScreen({super.key});

  @override
  ConsumerState<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends ConsumerState<DashboardScreen> {
  bool _passwordChecked = false;

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

  int _n(dynamic v) => (v as num?)?.toInt() ?? 0;

  String _formatFecha(String? raw) {
    if (raw == null || raw.isEmpty) return '';
    try {
      final dt = DateTime.parse(raw).toLocal();
      return '${dt.day.toString().padLeft(2, '0')}/'
          '${dt.month.toString().padLeft(2, '0')}/'
          '${dt.year}';
    } catch (_) {
      return raw;
    }
  }

  void _refrescar() {
    ref.invalidate(dashboardProvider);
    ref.invalidate(viajesActivosProvider);
  }

  @override
  Widget build(BuildContext context) {
    final dashAsync          = ref.watch(dashboardProvider);
    final viajesActivosAsync = ref.watch(viajesActivosProvider);

    return AdminPageScaffold(
      navItem:  AdminNavItem.dashboard,
      subtitle: kIsWeb ? 'Resumen operativo en tiempo real' : null,
      actions: [
        IconButton(
          tooltip: 'Actualizar',
          icon:    const Icon(Icons.refresh),
          onPressed: _refrescar,
        ),
        if (!kIsWeb)
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () => confirmarCerrarSesion(context, ref),
          ),
      ],
      body: dashAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppTheme.primary),
        ),
        error: (e, _) => ErrorStateWidget(
          error: e,
          onRetry: () => ref.invalidate(dashboardProvider),
        ),
        data: (data) {
          final totales      = data['totales']           as Map<String, dynamic>? ?? {};
          final hoy          = data['hoy']               as Map<String, dynamic>? ?? {};
          final mes          = data['mes_actual']        as Map<String, dynamic>? ?? {};
          final proximas     = data['proximas_reservas'] as List<dynamic>?        ?? [];
          final ocupacionHoy = data['ocupacion_hoy']    as List<dynamic>?         ?? [];
          final pendientes   = _n(data['pendientes_aprobacion']);

          final banner = pendientes > 0
              ? _PendientesBanner(pendientes: pendientes)
              : null;

          final resumenGeneral = GridView.count(
            crossAxisCount:   Responsive.gridColumns(context),
            shrinkWrap:       true,
            physics:          const NeverScrollableScrollPhysics(),
            crossAxisSpacing: 12,
            mainAxisSpacing:  12,
            childAspectRatio: 1.6,
            children: [
              _TotalCard(
                label: 'Embarcaciones',
                value: '${_n(totales['embarcaciones'])}',
                sub:   '${_n(totales['disponibles'])} disponibles',
                icon:  Icons.directions_boat_rounded,
                color: AppTheme.primary,
                onTap: () => context.push('/admin/embarcaciones'),
              ),
              _TotalCard(
                label: 'Usuarios',
                value: '${_n(totales['usuarios'])}',
                sub:   'registrados',
                icon:  Icons.people_outline,
                color: AppTheme.info,
                onTap: () => context.push('/admin/usuarios'),
              ),
              _TotalCard(
                label: 'Total reservas',
                value: '${_n(totales['reservas_total'])}',
                sub:   'históricas',
                icon:  Icons.book_online_outlined,
                color: AppTheme.success,
                onTap: () => context.push('/admin/reservas'),
              ),
              _TotalCard(
                label: 'Total pasajeros',
                value: '${_n(totales['pasajeros_total'])}',
                sub:   'transportados',
                icon:  Icons.group_outlined,
                color: AppTheme.warning,
              ),
            ],
          );

          final actividad = Row(
            children: [
              Expanded(
                child: _PeriodoCard(
                  titulo:     'Hoy',
                  reservas:   _n(hoy['reservas']),
                  pasajeros:  _n(hoy['pasajeros']),
                  canceladas: _n(hoy['canceladas']),
                  color:      AppTheme.primary,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: _PeriodoCard(
                  titulo:     'Este mes',
                  reservas:   _n(mes['reservas']),
                  pasajeros:  _n(mes['pasajeros']),
                  canceladas: _n(mes['canceladas']),
                  color:      AppTheme.info,
                ),
              ),
            ],
          );

          final viajesEnCurso = _ViajesEnCursoSection(async: viajesActivosAsync);
          final proximasReservas = _ProximasReservasSection(
            proximas: proximas,
            formatFecha: _formatFecha,
            n: _n,
          );

          if (kIsWeb) {
            final isDesktop = Responsive.isDesktop(context);
            final ocupacionCard = _OcupacionCard(ocupacionHoy: ocupacionHoy);

            return RefreshIndicator(
              onRefresh: () async => _refrescar(),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    if (banner != null) ...[banner, const SizedBox(height: 18)],
                    resumenGeneral,
                    const SizedBox(height: 18),
                    if (isDesktop)
                      IntrinsicHeight(
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            Expanded(flex: 3, child: ocupacionCard),
                            const SizedBox(width: 16),
                            Expanded(flex: 2, child: actividad),
                          ],
                        ),
                      )
                    else ...[
                      ocupacionCard,
                      const SizedBox(height: 16),
                      actividad,
                    ],
                    const SizedBox(height: 18),
                    if (isDesktop)
                      IntrinsicHeight(
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            Expanded(child: viajesEnCurso),
                            const SizedBox(width: 16),
                            Expanded(child: proximasReservas),
                          ],
                        ),
                      )
                    else ...[
                      viajesEnCurso,
                      const SizedBox(height: 16),
                      proximasReservas,
                    ],
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            );
          }

          // ── Móvil (sin cambios de comportamiento) ──────────────────────
          return RefreshIndicator(
            onRefresh: () async => _refrescar(),
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (banner != null) ...[banner, const SizedBox(height: 20)],
                  viajesEnCurso,
                  const SizedBox(height: 20),
                  const _SectionTitle('Resumen general'),
                  const SizedBox(height: 8),
                  resumenGeneral,
                  const SizedBox(height: 20),
                  const _SectionTitle('Actividad'),
                  const SizedBox(height: 8),
                  actividad,
                  const SizedBox(height: 20),
                  const _SectionTitle('Ocupación hoy'),
                  const SizedBox(height: 8),
                  _OcupacionMeters(ocupacionHoy: ocupacionHoy),
                  const SizedBox(height: 20),
                  proximasReservas,
                  const SizedBox(height: 32),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

// ── Widgets ───────────────────────────────────────────────────────────────────

class _PendientesBanner extends StatelessWidget {
  final int pendientes;
  const _PendientesBanner({required this.pendientes});

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(12),
      onTap: () => context.push('/admin/reservas'),
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color:        AppTheme.warning.withOpacity(0.12),
          borderRadius: BorderRadius.circular(12),
          border:       Border.all(color: AppTheme.warning),
        ),
        child: Row(
          children: [
            const Icon(Icons.pending_actions, color: AppTheme.warning, size: 22),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '$pendientes reserva${pendientes == 1 ? '' : 's'} pendiente${pendientes == 1 ? '' : 's'} de aprobar',
                    style: const TextStyle(
                      fontWeight: FontWeight.w700,
                      fontSize:   13,
                      color:      AppTheme.primary,
                    ),
                  ),
                  const Text(
                    'Toca para revisarlas',
                    style: TextStyle(fontSize: 11, color: AppTheme.textMuted),
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right, color: AppTheme.textMuted),
          ],
        ),
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String text;
  const _SectionTitle(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: const TextStyle(
        fontSize:   16,
        fontWeight: FontWeight.w700,
        color:      AppTheme.primary,
      ),
    );
  }
}

class _CardShell extends StatelessWidget {
  final String  title;
  final String? subtitle;
  final Widget  child;
  final Widget? trailing;

  const _CardShell({
    required this.title,
    required this.child,
    this.subtitle,
    this.trailing,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color:        Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 8, offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title,
                        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700, color: AppTheme.primary)),
                    if (subtitle != null)
                      Text(subtitle!,
                          style: const TextStyle(fontSize: 11.5, color: AppTheme.textMuted)),
                  ],
                ),
              ),
              if (trailing != null) trailing!,
            ],
          ),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }
}

class _TotalCard extends StatelessWidget {
  final String       label;
  final String       value;
  final String       sub;
  final IconData     icon;
  final Color        color;
  final VoidCallback? onTap;

  const _TotalCard({
    required this.label,
    required this.value,
    required this.sub,
    required this.icon,
    required this.color,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final card = Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color:        Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color:     Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset:    const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            padding:    const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color:        color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment:  MainAxisAlignment.center,
              children: [
                Text(
                  value,
                  style: TextStyle(
                    fontSize:   22,
                    fontWeight: FontWeight.w800,
                    color:      color,
                  ),
                ),
                Text(
                  label,
                  style: const TextStyle(
                    fontSize:   11,
                    fontWeight: FontWeight.w600,
                    color:      AppTheme.primary,
                  ),
                ),
                Text(
                  sub,
                  style: const TextStyle(
                    fontSize: 10,
                    color:    AppTheme.textMuted,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );

    if (onTap == null) return card;

    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap:        onTap,
        child:        card,
      ),
    );
  }
}

class _PeriodoCard extends StatelessWidget {
  final String titulo;
  final int    reservas;
  final int    pasajeros;
  final int    canceladas;
  final Color  color;

  const _PeriodoCard({
    required this.titulo,
    required this.reservas,
    required this.pasajeros,
    required this.canceladas,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color:        color,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            titulo,
            style: const TextStyle(
              color:      Colors.white70,
              fontSize:   12,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            '$reservas',
            style: const TextStyle(
              color:      Colors.white,
              fontSize:   28,
              fontWeight: FontWeight.w800,
            ),
          ),
          const Text(
            'reservas',
            style: TextStyle(color: Colors.white70, fontSize: 11),
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              _MiniStat(label: 'Pax',        value: '$pasajeros'),
              const SizedBox(width: 12),
              _MiniStat(label: 'Canceladas', value: '$canceladas'),
            ],
          ),
        ],
      ),
    );
  }
}

class _MiniStat extends StatelessWidget {
  final String label;
  final String value;
  const _MiniStat({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(value,
            style: const TextStyle(
              color:      Colors.white,
              fontSize:   16,
              fontWeight: FontWeight.w700,
            )),
        Text(label,
            style: const TextStyle(color: Colors.white60, fontSize: 10)),
      ],
    );
  }
}

class _ViajesEnCursoSection extends StatelessWidget {
  final AsyncValue<Map<String, dynamic>> async;
  const _ViajesEnCursoSection({required this.async});

  @override
  Widget build(BuildContext context) {
    return async.when(
      loading: () => const SizedBox.shrink(),
      error:   (_, __) => const SizedBox.shrink(),
      data: (data) {
        final viajes = data['data'] as List<dynamic>? ?? [];
        if (viajes.isEmpty) {
          if (!kIsWeb) return const SizedBox.shrink();
          return const _CardShell(
            title: 'Viajes en curso',
            subtitle: 'Ninguno en este momento',
            child: Text('No hay viajes activos ahora mismo.',
                style: TextStyle(fontSize: 12.5, color: AppTheme.textMuted)),
          );
        }

        final lista = Column(
          children: viajes.map((v) {
            final viaje = v as Map<String, dynamic>;
            final estadoViaje = viaje['estado'] as String? ?? '';
            final color = estadoViaje == 'en_curso' ? AppTheme.info : AppTheme.warning;

            return InkWell(
              borderRadius: BorderRadius.circular(10),
              onTap: () => context.push('/viajes/${viaje['id']}'),
              child: Container(
                margin: const EdgeInsets.only(bottom: 8),
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color:        kIsWeb ? AppTheme.background : Colors.white,
                  borderRadius: BorderRadius.circular(10),
                  boxShadow: kIsWeb
                      ? null
                      : [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 6)],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Icon(Icons.directions_boat, color: color, size: 18),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            viaje['embarcacion'] ?? '',
                            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                          ),
                          Text(
                            '${viaje['responsable'] ?? ''} · ${viaje['total_personas'] ?? 0} pax',
                            style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
                          ),
                          if (viaje['operador'] != null)
                            Text(
                              'Operador: ${viaje['operador']}',
                              style: const TextStyle(
                                fontSize:  11,
                                fontStyle: FontStyle.italic,
                                color:     AppTheme.textMuted,
                              ),
                            ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: color.withOpacity(0.1),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: color),
                      ),
                      child: Text(
                        estadoViaje.toUpperCase(),
                        style: TextStyle(fontSize: 9, fontWeight: FontWeight.w700, color: color),
                      ),
                    ),
                    const SizedBox(width: 4),
                    const Icon(Icons.chevron_right, color: AppTheme.textMuted),
                  ],
                ),
              ),
            );
          }).toList(),
        );

        if (!kIsWeb) {
          return Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const _SectionTitle('Viajes en curso'),
              const SizedBox(height: 4),
              const Text(
                'Toca uno para retomarlo sin volver a escanear el QR.',
                style: TextStyle(fontSize: 11, color: AppTheme.textMuted),
              ),
              const SizedBox(height: 8),
              lista,
            ],
          );
        }

        return _CardShell(
          title:    'Viajes en curso',
          subtitle: '${viajes.length} activo${viajes.length == 1 ? '' : 's'} ahora',
          child:    lista,
        );
      },
    );
  }
}

class _ProximasReservasSection extends StatelessWidget {
  final List<dynamic> proximas;
  final String Function(String?) formatFecha;
  final int Function(dynamic)    n;

  const _ProximasReservasSection({
    required this.proximas,
    required this.formatFecha,
    required this.n,
  });

  @override
  Widget build(BuildContext context) {
    if (proximas.isEmpty) {
      if (!kIsWeb) return const SizedBox.shrink();
      return const _CardShell(
        title:    'Próximas reservas',
        subtitle: 'Siguientes 24 h',
        child: Text('No hay reservas próximas.',
            style: TextStyle(fontSize: 12.5, color: AppTheme.textMuted)),
      );
    }

    final lista = Column(
      children: proximas.take(5).map((e) {
        final r = e as Map<String, dynamic>;
        return Container(
          margin: const EdgeInsets.only(bottom: 8),
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color:        kIsWeb ? AppTheme.background : Colors.white,
            borderRadius: BorderRadius.circular(10),
            boxShadow: kIsWeb
                ? null
                : [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 6)],
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: AppTheme.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.directions_boat, color: AppTheme.primary, size: 18),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(r['embarcacion'] ?? '',
                        style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                    Text(r['usuario'] ?? '',
                        style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
                  ],
                ),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    formatFecha(r['fecha'] as String?),
                    style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppTheme.primary),
                  ),
                  Text('${n(r['personas'])} pax',
                      style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                ],
              ),
            ],
          ),
        );
      }).toList(),
    );

    if (!kIsWeb) {
      return Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const _SectionTitle('Próximas reservas'),
          const SizedBox(height: 8),
          lista,
        ],
      );
    }

    return _CardShell(
      title:    'Próximas reservas',
      subtitle: 'Siguientes 24 h',
      child:    lista,
    );
  }
}

/// Lista de meters (usada en móvil, y como fallback si algún día se quiere
/// mostrar sin gráfico en web).
class _OcupacionMeters extends StatelessWidget {
  final List<dynamic> ocupacionHoy;
  const _OcupacionMeters({required this.ocupacionHoy});

  @override
  Widget build(BuildContext context) {
    if (ocupacionHoy.isEmpty) {
      return const Text('Sin horarios registrados hoy.',
          style: TextStyle(fontSize: 12.5, color: AppTheme.textMuted));
    }
    return Column(
      children: ocupacionHoy.map((e) {
        final em          = e as Map<String, dynamic>;
        final reservados  = (em['reservados'] as num?)?.toInt() ?? 0;
        final capacidad   = (em['capacidad'] as num?)?.toInt() ?? 0;
        final porcentaje  = (em['porcentaje'] as num?)?.toDouble() ?? 0.0;
        final horaInicio  = em['hora_inicio'] as String?;
        final horaFin     = em['hora_fin']    as String?;
        final tieneHorario = horaInicio != null && horaFin != null;
        final color = porcentaje > 80 ? AppTheme.error : AppTheme.success;

        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          padding: const EdgeInsets.all(14),
          decoration: BoxDecoration(
            color:        Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 8, offset: const Offset(0, 2)),
            ],
          ),
          child: Column(
            children: [
              Row(
                children: [
                  const Icon(Icons.directions_boat, color: AppTheme.primary, size: 18),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(em['nombre'] ?? '',
                            style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14, color: AppTheme.primary)),
                        if (tieneHorario)
                          Text('$horaInicio – $horaFin',
                              style: const TextStyle(fontSize: 11, color: AppTheme.textMuted)),
                      ],
                    ),
                  ),
                  Text('$reservados/$capacidad',
                      style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700, color: AppTheme.primary)),
                  const SizedBox(width: 8),
                  Text('${porcentaje.toStringAsFixed(0)}%',
                      style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: color)),
                ],
              ),
              const SizedBox(height: 8),
              ClipRRect(
                borderRadius: BorderRadius.circular(4),
                child: LinearProgressIndicator(
                  value:           porcentaje / 100,
                  backgroundColor: Colors.grey[200],
                  valueColor:      AlwaysStoppedAnimation(color),
                  minHeight:       6,
                ),
              ),
            ],
          ),
        );
      }).toList(),
    );
  }
}

/// Gráfico de barras (fl_chart) con la ocupación % por embarcación — solo web.
/// Umbral > 80% en rojo, igual que la lógica ya usada en el resto de la app.
class _OcupacionCard extends StatefulWidget {
  final List<dynamic> ocupacionHoy;
  const _OcupacionCard({required this.ocupacionHoy});

  @override
  State<_OcupacionCard> createState() => _OcupacionCardState();
}

class _OcupacionCardState extends State<_OcupacionCard> {
  int? _tocado;

  @override
  Widget build(BuildContext context) {
    final datos = widget.ocupacionHoy;

    return _CardShell(
      title:    'Ocupación hoy',
      subtitle: 'Por embarcación',
      trailing: Row(
        children: [
          _leyendaDot(AppTheme.success, '≤ 80%'),
          const SizedBox(width: 10),
          _leyendaDot(AppTheme.error, '> 80%'),
        ],
      ),
      child: datos.isEmpty
          ? const Padding(
              padding: EdgeInsets.symmetric(vertical: 24),
              child: Text('Sin horarios registrados hoy.',
                  style: TextStyle(fontSize: 12.5, color: AppTheme.textMuted)),
            )
          : SizedBox(
              height: 260,
              child: BarChart(
                BarChartData(
                  maxY: 100,
                  minY: 0,
                  alignment: BarChartAlignment.spaceAround,
                  gridData: FlGridData(
                    show: true,
                    drawVerticalLine: false,
                    horizontalInterval: 25,
                    getDrawingHorizontalLine: (_) => FlLine(color: Colors.grey.shade200, strokeWidth: 1),
                  ),
                  borderData: FlBorderData(show: false),
                  titlesData: FlTitlesData(
                    topTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                    rightTitles: const AxisTitles(sideTitles: SideTitles(showTitles: false)),
                    leftTitles: AxisTitles(
                      sideTitles: SideTitles(
                        showTitles:     true,
                        reservedSize:   34,
                        interval:       25,
                        getTitlesWidget: (value, meta) => Text('${value.toInt()}%',
                            style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
                      ),
                    ),
                    bottomTitles: AxisTitles(
                      sideTitles: SideTitles(
                        showTitles:   true,
                        reservedSize: 30,
                        getTitlesWidget: (value, meta) {
                          final i = value.toInt();
                          if (i < 0 || i >= datos.length) return const SizedBox.shrink();
                          final nombre = (datos[i]['nombre'] as String? ?? '').split(' ').first;
                          return Padding(
                            padding: const EdgeInsets.only(top: 6),
                            child: Text(nombre,
                                style: const TextStyle(fontSize: 10.5, color: AppTheme.textMuted)),
                          );
                        },
                      ),
                    ),
                  ),
                  barTouchData: BarTouchData(
                    touchTooltipData: BarTouchTooltipData(
                      getTooltipColor: (_) => AppTheme.primary,
                      getTooltipItem: (group, groupIdx, rod, rodIdx) {
                        final em = datos[group.x.toInt()] as Map<String, dynamic>;
                        return BarTooltipItem(
                          '${em['nombre'] ?? ''}\n',
                          const TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 11.5),
                          children: [
                            TextSpan(
                              text: '${em['reservados'] ?? 0}/${em['capacidad'] ?? 0} · ${rod.toY.toStringAsFixed(0)}%',
                              style: const TextStyle(color: Colors.white70, fontWeight: FontWeight.w500, fontSize: 10.5),
                            ),
                          ],
                        );
                      },
                    ),
                    touchCallback: (event, response) {
                      if (!event.isInterestedForInteractions || response?.spot == null) {
                        setState(() => _tocado = null);
                        return;
                      }
                      setState(() => _tocado = response!.spot!.touchedBarGroupIndex);
                    },
                  ),
                  barGroups: List.generate(datos.length, (i) {
                    final em  = datos[i] as Map<String, dynamic>;
                    final pct = (em['porcentaje'] as num?)?.toDouble() ?? 0.0;
                    final color = pct > 80 ? AppTheme.error : AppTheme.success;
                    final resaltado = _tocado == null || _tocado == i;
                    return BarChartGroupData(
                      x: i,
                      barRods: [
                        BarChartRodData(
                          toY:       pct,
                          width:     22,
                          color:     resaltado ? color : color.withOpacity(0.35),
                          borderRadius: const BorderRadius.vertical(top: Radius.circular(4)),
                          backDrawRodData: BackgroundBarChartRodData(
                            show: true,
                            toY:  100,
                            color: const Color(0xFFF1F2F4),
                          ),
                        ),
                      ],
                    );
                  }),
                ),
              ),
            ),
    );
  }

  Widget _leyendaDot(Color color, String label) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(width: 8, height: 8, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        const SizedBox(width: 5),
        Text(label, style: const TextStyle(fontSize: 11, color: AppTheme.textMuted, fontWeight: FontWeight.w600)),
      ],
    );
  }
}
