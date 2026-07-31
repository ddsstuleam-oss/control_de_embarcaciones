import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/empty_state_widget.dart';
import '../../../../core/widgets/web_centered.dart';

final actividadProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.perfilActividades);
  if (response.statusCode == 200) {
    return response.data['data'] ?? [];
  }
  throw Exception('Error al cargar actividades');
});

class ActividadScreen extends ConsumerWidget {
  const ActividadScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final actividadAsync = ref.watch(actividadProvider);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Historial de actividad'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => ref.invalidate(actividadProvider),
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 480,
        child: actividadAsync.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (e, _) => Center(child: Text('Error: $e')),
          data: (actividades) => actividades.isEmpty
              ? const EmptyStateWidget(
                  icon: Icons.history,
                  titulo: 'Sin actividad',
                  mensaje: 'No hay actividad registrada\nen tu cuenta aún.',
                )
              : RefreshIndicator(
                  onRefresh: () async => ref.invalidate(actividadProvider),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: actividades.length,
                    itemBuilder: (_, i) => _ActividadItem(
                      actividad: actividades[i] as Map<String, dynamic>,
                      isLast: i == actividades.length - 1,
                    ),
                  ),
                ),
        ),
      ),
    );
  }
}

class _ActividadItem extends StatelessWidget {
  final Map<String, dynamic> actividad;
  final bool isLast;

  const _ActividadItem({required this.actividad, required this.isLast});

  IconData get _icon {
    final accion = actividad['accion'] as String? ?? '';
    if (accion.contains('reserva_creada')) return Icons.add_circle_outline;
    if (accion.contains('reserva_cancelada')) return Icons.cancel_outlined;
    if (accion.contains('reserva_completada'))
      return Icons.check_circle_outline;
    if (accion.contains('boleto_validado')) return Icons.qr_code;
    if (accion.contains('boleto_cancelado')) return Icons.qr_code_2;
    if (accion.contains('cuenta_creada')) return Icons.person_add_outlined;
    if (accion.contains('cuenta_activada')) return Icons.lock_open_outlined;
    if (accion.contains('cuenta_desactivada')) return Icons.lock_outlined;
    return Icons.info_outline;
  }

  Color get _color {
    final accion = actividad['accion'] as String? ?? '';
    if (accion.contains('creada') ||
        accion.contains('activada') ||
        accion.contains('validado')) return AppTheme.success;
    if (accion.contains('cancelada') ||
        accion.contains('desactivada') ||
        accion.contains('cancelado')) return AppTheme.error;
    if (accion.contains('completada')) return AppTheme.info;
    return AppTheme.primary;
  }

  String get _accionLabel {
    final accion = actividad['accion'] as String? ?? '';
    return accion
        .replaceAll('_', ' ')
        .split(' ')
        .map((w) => w.isNotEmpty ? '${w[0].toUpperCase()}${w.substring(1)}' : w)
        .join(' ');
  }

  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Línea de tiempo
          Column(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: _color.withOpacity(0.12),
                  shape: BoxShape.circle,
                  border: Border.all(color: _color.withOpacity(0.4)),
                ),
                child: Icon(_icon, color: _color, size: 20),
              ),
              if (!isLast)
                Expanded(
                  child: Container(
                    width: 2,
                    color: Colors.grey[200],
                  ),
                ),
            ],
          ),

          const SizedBox(width: 12),

          // Contenido
          Expanded(
            child: Container(
              margin: const EdgeInsets.only(bottom: 16),
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.04),
                    blurRadius: 6,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          _accionLabel,
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: _color,
                          ),
                        ),
                      ),
                      Text(
                        actividad['fecha'] ?? '',
                        style: const TextStyle(
                          fontSize: 10,
                          color: AppTheme.textMuted,
                        ),
                      ),
                    ],
                  ),
                  if (actividad['descripcion'] != null) ...[
                    const SizedBox(height: 4),
                    Text(
                      actividad['descripcion'] ?? '',
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppTheme.textMuted,
                        height: 1.4,
                      ),
                    ),
                  ],
                  if (actividad['dispositivo'] != null) ...[
                    const SizedBox(height: 6),
                    Row(
                      children: [
                        const Icon(Icons.devices_outlined,
                            size: 12, color: AppTheme.textMuted),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            actividad['dispositivo'] ?? '',
                            style: const TextStyle(
                              fontSize: 10,
                              color: AppTheme.textMuted,
                            ),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
