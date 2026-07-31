import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/boleto_utils.dart';
import '../../../../core/widgets/empty_state_widget.dart';
import '../../../../core/widgets/reserva_detalle_sheet.dart';
import '../../../../core/widgets/web_centered.dart';

final operadorReservasHoyProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.reservasHoy);
  if (response.statusCode == 200) {
    return response.data['data'] ?? [];
  }
  throw Exception('Error al cargar reservas');
});

class OperadorReservasScreen extends ConsumerStatefulWidget {
  const OperadorReservasScreen({super.key});

  @override
  ConsumerState<OperadorReservasScreen> createState() =>
      _OperadorReservasScreenState();
}

class _OperadorReservasScreenState
    extends ConsumerState<OperadorReservasScreen> {
  String _buscar = '';

  @override
  Widget build(BuildContext context) {
    final reservasAsync = ref.watch(operadorReservasHoyProvider);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Reservas de hoy'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => ref.invalidate(operadorReservasHoyProvider),
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 480,
        child: Column(
          children: [
            // Buscador
            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(12),
              child: TextField(
                onChanged: (v) => setState(() => _buscar = v),
                decoration: InputDecoration(
                  hintText: 'Buscar por embarcación o pasajero...',
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
                        onPressed: () =>
                            ref.invalidate(operadorReservasHoyProvider),
                        child: const Text('Reintentar'),
                      ),
                    ],
                  ),
                ),
                data: (reservas) {
                  final filtradas = reservas.where((r) {
                    final reserva = r as Map<String, dynamic>;
                    final embarcacion =
                        reserva['embarcacion'] as Map<String, dynamic>?;
                    final pasajeros =
                        reserva['pasajeros'] as List<dynamic>? ?? [];

                    if (_buscar.isEmpty) return true;

                    final matchEmb = (embarcacion?['nombre'] as String? ?? '')
                        .toLowerCase()
                        .contains(_buscar.toLowerCase());

                    final matchPax = pasajeros.any((p) =>
                        ((p as Map<String, dynamic>)['nombre'] as String? ?? '')
                            .toLowerCase()
                            .contains(_buscar.toLowerCase()));

                    return matchEmb || matchPax;
                  }).toList();

                  if (filtradas.isEmpty) {
                    return const EmptyStateWidget(
                      icon: Icons.inbox_outlined,
                      titulo: 'Sin reservas',
                      mensaje:
                          'No hay reservas para hoy\no no coinciden con la búsqueda.',
                    );
                  }

                  return RefreshIndicator(
                    onRefresh: () async =>
                        ref.invalidate(operadorReservasHoyProvider),
                    child: ListView.builder(
                      padding: const EdgeInsets.all(12),
                      itemCount: filtradas.length,
                      itemBuilder: (_, i) => _ReservaCard(
                        reserva: filtradas[i] as Map<String, dynamic>,
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ReservaCard extends StatelessWidget {
  final Map<String, dynamic> reserva;
  const _ReservaCard({required this.reserva});

  Color _estadoColor(String estado) {
    switch (estado) {
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

  void _mostrarDetalle(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => ReservaDetalleSheet(reserva: reserva),
    );
  }

  @override
  Widget build(BuildContext context) {
    final embarcacion = reserva['embarcacion'] as Map<String, dynamic>?;
    final boleto = reserva['boleto'] as Map<String, dynamic>?;
    final estado = reserva['estado'] as String? ?? '';
    final personas = (reserva['total_personas'] as num?)?.toInt() ?? 0;
    final estadoBoleto = boleto?['estado'] as String? ?? 'sin boleto';
    final color = _estadoColor(estado);

    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: InkWell(
        onTap: () => _mostrarDetalle(context),
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: AppTheme.primary.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Icon(
                  Icons.directions_boat,
                  color: AppTheme.primary,
                  size: 20,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      embarcacion?['nombre'] ?? '',
                      style: const TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.primary,
                      ),
                    ),
                    Text(
                      '$personas pasajero${personas > 1 ? 's' : ''}'
                      '${reserva['hora_inicio'] != null ? ' · ${reserva['hora_inicio']} — ${reserva['hora_fin']}' : ''}',
                      style: const TextStyle(
                        fontSize: 12,
                        color: AppTheme.textMuted,
                      ),
                    ),
                  ],
                ),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: color.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: color),
                    ),
                    child: Text(
                      estado.toUpperCase(),
                      style: TextStyle(
                        fontSize: 9,
                        fontWeight: FontWeight.w700,
                        color: color,
                      ),
                    ),
                  ),
                  // El estado del boleto solo aporta info distinta de la
                  // reserva mientras está confirmada (válido vs. usado);
                  // en el resto de estados es redundante (p. ej. vencida/vencido).
                  if (estado == 'confirmada') ...[
                    const SizedBox(height: 4),
                    _BoletoChip(estado: estadoBoleto),
                  ],
                ],
              ),
              const SizedBox(width: 4),
              const Icon(Icons.chevron_right, color: AppTheme.textMuted),
            ],
          ),
        ),
      ),
    );
  }
}

class _BoletoChip extends StatelessWidget {
  final String estado;
  const _BoletoChip({required this.estado});

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
