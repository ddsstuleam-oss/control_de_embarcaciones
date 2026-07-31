import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/boleto_utils.dart';
import '../../../../core/widgets/empty_state_widget.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../../features/auth/providers/auth_provider.dart';

final operadorPerfilProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.perfil);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar perfil');
});

final reservasHoyProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.reservasHoy);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar reservas');
});

final viajesActivosProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.viajesActivos);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar viajes en curso');
});

// ← ConsumerStatefulWidget para check de contraseña
class OperadorHomeScreen extends ConsumerStatefulWidget {
  const OperadorHomeScreen({super.key});

  @override
  ConsumerState<OperadorHomeScreen> createState() => _OperadorHomeScreenState();
}

class _OperadorHomeScreenState extends ConsumerState<OperadorHomeScreen> {
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

  Future<void> _confirmarLogout(BuildContext context, WidgetRef ref) async {
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
    if (confirm == true) {
      await ref.read(authProvider.notifier).logout();
    }
  }

  @override
  Widget build(BuildContext context) {
    final perfilAsync = ref.watch(operadorPerfilProvider);
    final reservasAsync = ref.watch(reservasHoyProvider);
    final viajesActivosAsync = ref.watch(viajesActivosProvider);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Panel Operador'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Cerrar sesión',
            onPressed: () => _confirmarLogout(context, ref),
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 560,
        child: RefreshIndicator(
          onRefresh: () async {
            ref.invalidate(operadorPerfilProvider);
            ref.invalidate(reservasHoyProvider);
            ref.invalidate(viajesActivosProvider);
          },
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // ── Header operador ──────────────────────────────
                perfilAsync.when(
                  loading: () => const _HeaderShimmer(),
                  error: (_, __) => const SizedBox.shrink(),
                  data: (perfil) {
                    final usuario =
                        perfil['usuario'] as Map<String, dynamic>? ?? {};
                    return Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [AppTheme.primary, Color(0xFF16213E)],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Row(
                        children: [
                          Stack(
                            alignment: Alignment.bottomRight,
                            children: [
                              CircleAvatar(
                                radius: 32,
                                backgroundColor: AppTheme.accent,
                                child: Text(
                                  (usuario['nombre'] as String? ?? 'O')[0]
                                      .toUpperCase(),
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 24,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                              ),
                              Container(
                                padding: const EdgeInsets.all(3),
                                decoration: BoxDecoration(
                                  color: Colors.blue[300],
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: AppTheme.primary,
                                    width: 2,
                                  ),
                                ),
                                child: const Icon(
                                  Icons.qr_code_scanner,
                                  color: Colors.white,
                                  size: 12,
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  usuario['nombre'] ?? '',
                                  style: const TextStyle(
                                    color: Colors.white,
                                    fontSize: 16,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 10,
                                    vertical: 3,
                                  ),
                                  decoration: BoxDecoration(
                                    color: Colors.blue.withOpacity(0.2),
                                    borderRadius: BorderRadius.circular(10),
                                    border: Border.all(
                                      color: Colors.blue.withOpacity(0.5),
                                    ),
                                  ),
                                  child: const Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Icon(Icons.qr_code_scanner,
                                          color: Colors.blue, size: 11),
                                      SizedBox(width: 4),
                                      Text(
                                        'OPERADOR',
                                        style: TextStyle(
                                          color: Colors.blue,
                                          fontSize: 10,
                                          fontWeight: FontWeight.w700,
                                          letterSpacing: 1,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  usuario['email'] ?? '',
                                  style: const TextStyle(
                                    color: Colors.white54,
                                    fontSize: 11,
                                  ),
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    );
                  },
                ),

                const SizedBox(height: 20),

                const Text(
                  'Resumen del día',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.primary,
                  ),
                ),
                const SizedBox(height: 10),

                reservasAsync.when(
                  loading: () => const Center(
                    child: CircularProgressIndicator(color: AppTheme.primary),
                  ),
                  error: (e, _) => Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: AppTheme.error.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.error_outline, color: AppTheme.error),
                        SizedBox(width: 8),
                        Text('Error al cargar reservas del día'),
                      ],
                    ),
                  ),
                  data: (data) {
                    final reservas = data['data'] as List<dynamic>? ?? [];
                    final total = reservas.length;
                    final pasajeros = reservas.fold<int>(
                      0,
                      (sum, r) =>
                          sum +
                          ((r as Map<String, dynamic>)['total_personas']
                                  as num?)!
                              .toInt(),
                    );
                    final validados = reservas.where((r) {
                      final b = (r as Map<String, dynamic>)['boleto']
                          as Map<String, dynamic>?;
                      return b?['estado'] == 'usado';
                    }).length;

                    return Column(
                      children: [
                        Row(
                          children: [
                            _ResumenCard(
                              label: 'Reservas hoy',
                              value: '$total',
                              icon: Icons.book_online,
                              color: AppTheme.primary,
                            ),
                            const SizedBox(width: 12),
                            _ResumenCard(
                              label: 'Pasajeros',
                              value: '$pasajeros',
                              icon: Icons.people_outline,
                              color: AppTheme.info,
                            ),
                            const SizedBox(width: 12),
                            _ResumenCard(
                              label: 'Embarcados',
                              value: '$validados',
                              icon: Icons.check_circle_outline,
                              color: AppTheme.success,
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        if (reservas.isNotEmpty) ...[
                          const Align(
                            alignment: Alignment.centerLeft,
                            child: Text(
                              'Reservas de hoy',
                              style: TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.primary,
                              ),
                            ),
                          ),
                          const SizedBox(height: 8),
                          ...reservas.map((r) {
                            final reserva = r as Map<String, dynamic>;
                            final embarcacion =
                                reserva['embarcacion'] as Map<String, dynamic>?;
                            final boleto =
                                reserva['boleto'] as Map<String, dynamic>?;
                            final personas =
                                (reserva['total_personas'] as num?)?.toInt() ??
                                    0;
                            final estadoBoleto =
                                boleto?['estado'] ?? 'sin boleto';

                            return Container(
                              margin: const EdgeInsets.only(bottom: 8),
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(12),
                                boxShadow: [
                                  BoxShadow(
                                    color: Colors.black.withOpacity(0.04),
                                    blurRadius: 6,
                                  ),
                                ],
                              ),
                              child: Row(
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
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          embarcacion?['nombre'] ?? '',
                                          style: const TextStyle(
                                            fontWeight: FontWeight.w600,
                                            fontSize: 13,
                                            color: AppTheme.primary,
                                          ),
                                        ),
                                        Text(
                                          '$personas pasajero${personas > 1 ? 's' : ''}',
                                          style: const TextStyle(
                                            fontSize: 11,
                                            color: AppTheme.textMuted,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  _BoletoEstadoBadge(estado: estadoBoleto),
                                ],
                              ),
                            );
                          }),
                        ] else
                          const EmptyStateWidget(
                            icon: Icons.inbox_outlined,
                            titulo: 'Sin reservas hoy',
                            mensaje:
                                'No hay reservas programadas\npara el día de hoy.',
                          ),
                      ],
                    );
                  },
                ),

                viajesActivosAsync.when(
                  loading: () => const SizedBox.shrink(),
                  error: (_, __) => const SizedBox.shrink(),
                  data: (data) {
                    final viajes = data['data'] as List<dynamic>? ?? [];
                    if (viajes.isEmpty) return const SizedBox.shrink();

                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const SizedBox(height: 20),
                        const Text(
                          'Viajes en curso',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.primary,
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Toca uno para retomarlo sin volver a escanear el QR.',
                          style: TextStyle(
                              fontSize: 11, color: AppTheme.textMuted),
                        ),
                        const SizedBox(height: 10),
                        ...viajes.map((v) {
                          final viaje = v as Map<String, dynamic>;
                          final estadoViaje = viaje['estado'] as String? ?? '';
                          final color = estadoViaje == 'en_curso'
                              ? AppTheme.info
                              : AppTheme.warning;

                          return Container(
                            margin: const EdgeInsets.only(bottom: 8),
                            child: InkWell(
                              borderRadius: BorderRadius.circular(12),
                              onTap: () =>
                                  context.push('/viajes/${viaje['id']}'),
                              child: Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  borderRadius: BorderRadius.circular(12),
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.04),
                                      blurRadius: 6,
                                    ),
                                  ],
                                ),
                                child: Row(
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        color: color.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(8),
                                      ),
                                      child: Icon(Icons.directions_boat,
                                          color: color, size: 18),
                                    ),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            viaje['embarcacion'] ?? '',
                                            style: const TextStyle(
                                              fontWeight: FontWeight.w600,
                                              fontSize: 13,
                                              color: AppTheme.primary,
                                            ),
                                          ),
                                          Text(
                                            '${viaje['responsable'] ?? ''} · '
                                            '${viaje['total_personas'] ?? 0} pax',
                                            style: const TextStyle(
                                              fontSize: 11,
                                              color: AppTheme.textMuted,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                    Container(
                                      padding: const EdgeInsets.symmetric(
                                          horizontal: 8, vertical: 3),
                                      decoration: BoxDecoration(
                                        color: color.withOpacity(0.1),
                                        borderRadius: BorderRadius.circular(10),
                                        border: Border.all(color: color),
                                      ),
                                      child: Text(
                                        estadoViaje.toUpperCase(),
                                        style: TextStyle(
                                          fontSize: 9,
                                          fontWeight: FontWeight.w700,
                                          color: color,
                                        ),
                                      ),
                                    ),
                                    const SizedBox(width: 4),
                                    const Icon(Icons.chevron_right,
                                        size: 18, color: AppTheme.textMuted),
                                  ],
                                ),
                              ),
                            ),
                          );
                        }),
                      ],
                    );
                  },
                ),

                const SizedBox(height: 20),

                const Text(
                  'Acciones',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.primary,
                  ),
                ),
                const SizedBox(height: 10),

                Row(
                  children: [
                    Expanded(
                      child: _AccionCard(
                        icon: Icons.qr_code_scanner,
                        label: 'Escanear QR',
                        sublabel: 'Validar boleto',
                        color: AppTheme.primary,
                        onTap: () => context.push('/scanner'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _AccionCard(
                        icon: Icons.book_online,
                        label: 'Reservas',
                        sublabel: 'Ver detalle',
                        color: AppTheme.info,
                        onTap: () => context.push('/operador/reservas'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: _AccionCard(
                        icon: Icons.person_outline,
                        label: 'Mi perfil',
                        sublabel: 'Ver información',
                        color: AppTheme.success,
                        onTap: () => context.push('/operador/perfil'),
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 32),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ── Widgets ───────────────────────────────────────────────────────────────────

class _HeaderShimmer extends StatelessWidget {
  const _HeaderShimmer();

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 100,
      decoration: BoxDecoration(
        color: Colors.grey[200],
        borderRadius: BorderRadius.circular(16),
      ),
    );
  }
}

class _ResumenCard extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  final Color color;

  const _ResumenCard({
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(12),
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
            Icon(icon, color: color, size: 22),
            const SizedBox(height: 6),
            Text(
              value,
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.w700,
                color: color,
              ),
            ),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10,
                color: AppTheme.textMuted,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}

class _AccionCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final String sublabel;
  final Color color;
  final VoidCallback onTap;

  const _AccionCard({
    required this.icon,
    required this.label,
    required this.sublabel,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(16),
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
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: color.withOpacity(0.1),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: color, size: 28),
            ),
            const SizedBox(height: 10),
            Text(
              label,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: AppTheme.primary,
              ),
            ),
            Text(
              sublabel,
              style: const TextStyle(
                fontSize: 11,
                color: AppTheme.textMuted,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _BoletoEstadoBadge extends StatelessWidget {
  final String estado;
  const _BoletoEstadoBadge({required this.estado});

  Color get _color {
    switch (estado) {
      case 'valido':
        return AppTheme.success;
      case 'usado':
        return AppTheme.info;
      case 'cancelado':
        return AppTheme.error;
      case 'vencido':
        return AppTheme.textMuted;
      default:
        return AppTheme.textMuted;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: _color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: _color),
      ),
      child: Text(
        estadoBoletoLabel(estado).toUpperCase(),
        style: TextStyle(
          fontSize: 9,
          fontWeight: FontWeight.w700,
          color: _color,
        ),
      ),
    );
  }
}
