import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../../core/api/api_client.dart';
import '../../../../../core/api/api_endpoints.dart';
import '../../../../../core/theme/app_theme.dart';
import '../../../../../core/widgets/admin_nav.dart';
import '../../../../../core/widgets/admin_page_scaffold.dart';
import '../../../../../core/widgets/empty_state_widget.dart';
import '../../../../../core/widgets/reserva_detalle_sheet.dart';

final adminReservasProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.adminReservas);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar reservas');
});

class AdminReservasScreen extends ConsumerStatefulWidget {
  const AdminReservasScreen({super.key});

  @override
  ConsumerState<AdminReservasScreen> createState() =>
      _AdminReservasScreenState();
}

class _AdminReservasScreenState extends ConsumerState<AdminReservasScreen> {
  String _estadoFiltro = 'todos';
  String _buscar = '';
  DateTime? _fechaFiltro;

  bool _mismoDia(DateTime a, DateTime b) =>
      a.year == b.year && a.month == b.month && a.day == b.day;

  Future<void> _seleccionarFechaFiltro() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _fechaFiltro ?? DateTime.now(),
      firstDate: DateTime(2020),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: const ColorScheme.light(primary: AppTheme.primary),
        ),
        child: child!,
      ),
    );
    if (picked != null) setState(() => _fechaFiltro = picked);
  }

  @override
  Widget build(BuildContext context) {
    final reservasAsync = ref.watch(adminReservasProvider);

    return AdminPageScaffold(
      navItem: AdminNavItem.reservas,
      actions: [
        IconButton(
          icon: const Icon(Icons.refresh),
          onPressed: () => ref.invalidate(adminReservasProvider),
        ),
      ],
      body: Column(
        children: [
          // Filtros
          Container(
            color: Colors.white,
            padding: const EdgeInsets.all(12),
            child: Column(
              children: [
                TextField(
                  onChanged: (v) => setState(() => _buscar = v),
                  decoration: InputDecoration(
                    hintText: 'Buscar por embarcación o usuario...',
                    prefixIcon: const Icon(Icons.search),
                    suffixIcon: _buscar.isNotEmpty
                        ? IconButton(
                            icon: const Icon(Icons.close),
                            onPressed: () => setState(() => _buscar = ''),
                          )
                        : null,
                    isDense: true,
                    filled: true,
                    fillColor: Colors.grey[100],
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide: BorderSide.none,
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      'todos',
                      'pendiente',
                      'confirmada',
                      'rechazada',
                      'cancelada',
                      'completada',
                      'vencida',
                    ]
                        .map((e) => Padding(
                              padding: const EdgeInsets.only(right: 8),
                              child: FilterChip(
                                label: Text(e.toUpperCase()),
                                selected: _estadoFiltro == e,
                                onSelected: (_) =>
                                    setState(() => _estadoFiltro = e),
                                selectedColor:
                                    AppTheme.primary.withOpacity(0.15),
                                checkmarkColor: AppTheme.primary,
                                labelStyle: TextStyle(
                                  color: _estadoFiltro == e
                                      ? AppTheme.primary
                                      : AppTheme.textMuted,
                                  fontWeight: FontWeight.w600,
                                  fontSize: 11,
                                ),
                              ),
                            ))
                        .toList(),
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Expanded(
                      child: InkWell(
                        onTap: _seleccionarFechaFiltro,
                        borderRadius: BorderRadius.circular(10),
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 10),
                          decoration: BoxDecoration(
                            color: Colors.grey[100],
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Row(
                            children: [
                              Icon(Icons.calendar_today,
                                  size: 16,
                                  color: _fechaFiltro != null
                                      ? AppTheme.primary
                                      : AppTheme.textMuted),
                              const SizedBox(width: 8),
                              Text(
                                _fechaFiltro != null
                                    ? DateFormat('dd/MM/yyyy')
                                        .format(_fechaFiltro!)
                                    : 'Filtrar por fecha',
                                style: TextStyle(
                                  fontSize: 13,
                                  fontWeight: FontWeight.w600,
                                  color: _fechaFiltro != null
                                      ? AppTheme.primary
                                      : AppTheme.textMuted,
                                ),
                              ),
                              if (_fechaFiltro != null) ...[
                                const Spacer(),
                                GestureDetector(
                                  onTap: () =>
                                      setState(() => _fechaFiltro = null),
                                  child: const Icon(Icons.close,
                                      size: 16, color: AppTheme.textMuted),
                                ),
                              ],
                            ],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // Lista
          Expanded(
            child: reservasAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppTheme.primary),
              ),
              error: (e, _) => Center(
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.error_outline,
                        size: 48, color: AppTheme.error),
                    const SizedBox(height: 12),
                    Text('Error: $e'),
                    const SizedBox(height: 12),
                    ElevatedButton(
                      onPressed: () => ref.invalidate(adminReservasProvider),
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              ),
              data: (data) {
                final List reservas = data['data'] ?? [];

                final filtradas = reservas.where((r) {
                  final reserva = r as Map<String, dynamic>;
                  final embarcacion =
                      reserva['embarcacion'] as Map<String, dynamic>?;
                  final usuario = reserva['user'] as Map<String, dynamic>?;

                  final matchEstado = _estadoFiltro == 'todos' ||
                      reserva['estado'] == _estadoFiltro;

                  final matchBuscar = _buscar.isEmpty ||
                      (embarcacion?['nombre'] as String? ?? '')
                          .toLowerCase()
                          .contains(_buscar.toLowerCase()) ||
                      (usuario?['nombre'] as String? ?? '')
                          .toLowerCase()
                          .contains(_buscar.toLowerCase());

                  final reservaFecha = reserva['fecha'] as String?;
                  final matchFecha = _fechaFiltro == null ||
                      (reservaFecha != null &&
                          _mismoDia(
                              DateTime.parse(reservaFecha), _fechaFiltro!));

                  return matchEstado && matchBuscar && matchFecha;
                }).toList();

                if (filtradas.isEmpty) {
                  return const EmptyStateWidget(
                    icon: Icons.book_online_outlined,
                    titulo: 'Sin reservas',
                    mensaje:
                        'No hay reservas que coincidan\ncon los filtros aplicados.',
                  );
                }

                return RefreshIndicator(
                  onRefresh: () async => ref.invalidate(adminReservasProvider),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(12),
                    itemCount: filtradas.length,
                    itemBuilder: (_, i) {
                      final r = filtradas[i] as Map<String, dynamic>;
                      return _AdminReservaCard(
                        reserva: r,
                        onCancelar: r['estado'] == 'confirmada'
                            ? () => _cancelar(context, r['id'] as int)
                            : null,
                        onAprobar: r['estado'] == 'pendiente'
                            ? () => _aprobar(context, r['id'] as int)
                            : null,
                        onRechazar: r['estado'] == 'pendiente'
                            ? () => _rechazar(context, r['id'] as int)
                            : null,
                        onEliminar: () => _eliminar(context, r['id'] as int),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Future<String?> _pedirMotivo(
    BuildContext context, {
    required String titulo,
    required String mensaje,
    required String boton,
  }) async {
    // Abrir un diálogo en la misma pasada síncrona del click (mientras el
    // mouse tracker de Flutter Web todavía procesa el hover/click del botón)
    // dispara una reentrancia en su actualización de dispositivo — se
    // difiere un tick para dejar que ese ciclo termine antes.
    if (kIsWeb) await Future<void>.delayed(Duration.zero);
    if (!context.mounted) return null;

    final controller = TextEditingController();
    final formKey = GlobalKey<FormState>();

    final motivo = await showDialog<String>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: Text(titulo),
        content: Form(
          key: formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(mensaje),
              const SizedBox(height: 16),
              TextFormField(
                controller: controller,
                autofocus: true,
                maxLines: 3,
                decoration: const InputDecoration(
                  labelText: 'Motivo',
                  hintText: 'Explica el motivo para notificar al usuario',
                  border: OutlineInputBorder(),
                ),
                validator: (v) => v == null || v.trim().isEmpty
                    ? 'El motivo es requerido'
                    : null,
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext),
            child: const Text('No'),
          ),
          ElevatedButton(
            onPressed: () {
              if (formKey.currentState!.validate()) {
                Navigator.pop(dialogContext, controller.text.trim());
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: AppTheme.error,
            ),
            child: Text(boton),
          ),
        ],
      ),
    );

    return motivo;
  }

  /// Extrae un mensaje legible del cuerpo de una respuesta de error, sin
  /// asumir que sea un Map (puede venir como List, String o null y romper
  /// al indexar con 'error'/'message').
  String _mensajeError(dynamic data, String fallback) {
    if (data is Map) {
      final msg = data['error'] ?? data['message'];
      if (msg is String && msg.trim().isNotEmpty) return msg;
    } else if (data is String && data.trim().isNotEmpty) {
      return data;
    }
    return fallback;
  }

  void _mostrarError(BuildContext context, Object e, String fallback) {
    if (!context.mounted) return;
    final mensaje = e is DioException
        ? _mensajeError(e.response?.data, e.message ?? fallback)
        : e.toString().replaceAll('Exception: ', '');
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(mensaje),
        backgroundColor: AppTheme.error,
      ),
    );
  }

  Future<void> _cancelar(BuildContext context, int id) async {
    try {
      final motivo = await _pedirMotivo(
        context,
        titulo: 'Cancelar reserva',
        mensaje:
            '¿Cancelar esta reserva? Se invalidará el boleto y se notificará al usuario por correo con el motivo indicado.',
        boton: 'Sí, cancelar',
      );

      if (motivo == null) return;

      final response = await ApiClient().dio.patch(
        ApiEndpoints.adminCancelarReserva(id),
        data: {'motivo': motivo},
      );
      if (response.statusCode != 200) {
        throw Exception(
          _mensajeError(response.data, 'Error al cancelar reserva'),
        );
      }
      ref.invalidate(adminReservasProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Reserva cancelada'),
            backgroundColor: AppTheme.success,
          ),
        );
      }
    } catch (e) {
      _mostrarError(context, e, 'Error al cancelar reserva');
    }
  }

  Future<void> _aprobar(BuildContext context, int id) async {
    try {
      // Ver nota de _pedirMotivo: abrir el diálogo en la misma pasada
      // síncrona del click revienta el mouse tracker en web.
      if (kIsWeb) await Future<void>.delayed(Duration.zero);
      if (!context.mounted) return;

      final confirm = await showDialog<bool>(
        context: context,
        builder: (dialogContext) => AlertDialog(
          title: const Text('Aprobar reserva'),
          content: const Text(
              '¿Aprobar esta reserva? Se generará el boleto QR y se notificará al usuario por correo.'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(dialogContext, false),
              child: const Text('No'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.pop(dialogContext, true),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.success,
              ),
              child: const Text('Sí, aprobar'),
            ),
          ],
        ),
      );

      if (confirm != true) return;

      final response =
          await ApiClient().dio.patch(ApiEndpoints.adminAprobarReserva(id));
      if (response.statusCode != 200) {
        throw Exception(
          _mensajeError(response.data, 'Error al aprobar reserva'),
        );
      }
      ref.invalidate(adminReservasProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Reserva aprobada'),
            backgroundColor: AppTheme.success,
          ),
        );
      }
    } catch (e) {
      _mostrarError(context, e, 'Error al aprobar reserva');
    }
  }

  Future<void> _rechazar(BuildContext context, int id) async {
    try {
      final motivo = await _pedirMotivo(
        context,
        titulo: 'Rechazar reserva',
        mensaje:
            '¿Rechazar esta reserva? Se liberará el horario y se notificará al usuario por correo con el motivo indicado.',
        boton: 'Sí, rechazar',
      );

      if (motivo == null) return;

      final response = await ApiClient().dio.patch(
        ApiEndpoints.adminRechazarReserva(id),
        data: {'motivo': motivo},
      );
      if (response.statusCode != 200) {
        throw Exception(
          _mensajeError(response.data, 'Error al rechazar reserva'),
        );
      }
      ref.invalidate(adminReservasProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Reserva rechazada'),
            backgroundColor: AppTheme.success,
          ),
        );
      }
    } catch (e) {
      _mostrarError(context, e, 'Error al rechazar reserva');
    }
  }

  Future<void> _eliminar(BuildContext context, int id) async {
    try {
      // Ver nota de _pedirMotivo: abrir el diálogo en la misma pasada
      // síncrona del click revienta el mouse tracker en web.
      if (kIsWeb) await Future<void>.delayed(Duration.zero);
      if (!context.mounted) return;

      final confirm = await showDialog<bool>(
        context: context,
        builder: (dialogContext) => AlertDialog(
          title: const Text('Eliminar reserva'),
          content: const Text('¿Eliminar esta reserva permanentemente?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(dialogContext, false),
              child: const Text('No'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.pop(dialogContext, true),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppTheme.error,
              ),
              child: const Text('Eliminar'),
            ),
          ],
        ),
      );

      if (confirm != true) return;

      final response =
          await ApiClient().dio.delete(ApiEndpoints.adminEliminarReserva(id));
      if (response.statusCode != 200 && response.statusCode != 204) {
        throw Exception(
          _mensajeError(response.data, 'Error al eliminar reserva'),
        );
      }
      ref.invalidate(adminReservasProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Reserva eliminada'),
            backgroundColor: AppTheme.success,
          ),
        );
      }
    } catch (e) {
      _mostrarError(context, e, 'Error al eliminar reserva');
    }
  }
}

class _AdminReservaCard extends StatelessWidget {
  final Map<String, dynamic> reserva;
  final VoidCallback? onCancelar;
  final VoidCallback? onAprobar;
  final VoidCallback? onRechazar;
  final VoidCallback onEliminar;

  const _AdminReservaCard({
    required this.reserva,
    this.onCancelar,
    this.onAprobar,
    this.onRechazar,
    required this.onEliminar,
  });

  Color get _estadoColor {
    switch (reserva['estado']) {
      case 'confirmada':
        return AppTheme.success;
      case 'pendiente':
        return AppTheme.warning;
      case 'rechazada':
        return AppTheme.error;
      case 'cancelada':
        return AppTheme.error;
      case 'completada':
        return AppTheme.info;
      case 'vencida':
        return AppTheme.textMuted;
      default:
        return AppTheme.warning;
    }
  }

  @override
  Widget build(BuildContext context) {
    final embarcacion = reserva['embarcacion'] as Map<String, dynamic>?;
    final usuario = reserva['user'] as Map<String, dynamic>?;
    final fecha = reserva['fecha'] as String? ?? '';
    final estado = reserva['estado'] as String? ?? '';
    final personas = reserva['total_personas'] as int? ?? 0;

    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () => _mostrarDetalle(context),
        child: Padding(
          padding: const EdgeInsets.all(12),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Text(
                    '#${(reserva['id'] as int).toString().padLeft(6, '0')}',
                    style: const TextStyle(
                      fontSize: 11,
                      color: AppTheme.textMuted,
                      fontFamily: 'monospace',
                    ),
                  ),
                  const Spacer(),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 2,
                    ),
                    decoration: BoxDecoration(
                      color: _estadoColor.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: _estadoColor),
                    ),
                    child: Text(
                      estado.toUpperCase(),
                      style: TextStyle(
                        fontSize: 9,
                        fontWeight: FontWeight.w700,
                        color: _estadoColor,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.directions_boat,
                      size: 14, color: AppTheme.primary),
                  const SizedBox(width: 4),
                  Expanded(
                    child: Text(
                      embarcacion?['nombre'] ?? '',
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.primary,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 4),
              Row(
                children: [
                  const Icon(Icons.person_outline,
                      size: 14, color: AppTheme.textMuted),
                  const SizedBox(width: 4),
                  Flexible(
                    child: Text(
                      usuario?['nombre'] ?? '',
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 13,
                        color: AppTheme.textMuted,
                      ),
                    ),
                  ),
                  const Spacer(),
                  const Icon(Icons.calendar_today,
                      size: 12, color: AppTheme.textMuted),
                  const SizedBox(width: 4),
                  Text(
                    fecha.isNotEmpty
                        ? DateFormat('dd/MM/yyyy').format(DateTime.parse(fecha))
                        : '',
                    style: const TextStyle(
                      fontSize: 12,
                      color: AppTheme.textMuted,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Icon(Icons.people_outline,
                      size: 12, color: AppTheme.textMuted),
                  const SizedBox(width: 4),
                  Text(
                    '$personas pax',
                    style: const TextStyle(
                      fontSize: 12,
                      color: AppTheme.textMuted,
                    ),
                  ),
                ],
              ),
              if (reserva['hora_inicio'] != null)
                Text(
                  '${reserva['hora_inicio']} — ${reserva['hora_fin']}',
                  style: const TextStyle(
                    fontSize: 11,
                    color: AppTheme.textMuted,
                  ),
                ),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  if (onAprobar != null)
                    TextButton.icon(
                      onPressed: onAprobar,
                      icon: const Icon(Icons.check_circle_outline,
                          size: 16, color: AppTheme.success),
                      label: const Text(
                        'Aprobar',
                        style: TextStyle(color: AppTheme.success, fontSize: 12),
                      ),
                      style: TextButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 8),
                      ),
                    ),
                  if (onRechazar != null)
                    TextButton.icon(
                      onPressed: onRechazar,
                      icon: const Icon(Icons.highlight_off,
                          size: 16, color: AppTheme.error),
                      label: const Text(
                        'Rechazar',
                        style: TextStyle(color: AppTheme.error, fontSize: 12),
                      ),
                      style: TextButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 8),
                      ),
                    ),
                  if (onCancelar != null)
                    TextButton.icon(
                      onPressed: onCancelar,
                      icon: const Icon(Icons.cancel_outlined,
                          size: 16, color: AppTheme.error),
                      label: const Text(
                        'Cancelar',
                        style: TextStyle(color: AppTheme.error, fontSize: 12),
                      ),
                      style: TextButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 8),
                      ),
                    ),
                  TextButton.icon(
                    onPressed: onEliminar,
                    icon: const Icon(Icons.delete_outline,
                        size: 16, color: AppTheme.error),
                    label: const Text(
                      'Eliminar',
                      style: TextStyle(color: AppTheme.error, fontSize: 12),
                    ),
                    style: TextButton.styleFrom(
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _mostrarDetalle(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => ReservaDetalleSheet(
        reserva: reserva,
        onAprobar: onAprobar,
        onRechazar: onRechazar,
        onCancelar: onCancelar,
      ),
    );
  }
}
