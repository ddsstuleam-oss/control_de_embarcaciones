import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:uleam_embarcaciones/core/widgets/empty_state_widget.dart';
import '../../providers/reserva_provider.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/boleto_utils.dart';
import '../../../../core/widgets/web_centered.dart';

class ReservasScreen extends ConsumerStatefulWidget {
  const ReservasScreen({super.key});

  @override
  ConsumerState<ReservasScreen> createState() => _ReservasScreenState();
}

class _ReservasScreenState extends ConsumerState<ReservasScreen> {
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

  List<Widget> _estadoChips() {
    return [
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
                onSelected: (_) => setState(() => _estadoFiltro = e),
                selectedColor: AppTheme.primary.withOpacity(0.15),
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
        .toList();
  }

  @override
  Widget build(BuildContext context) {
    final reservasAsync = ref.watch(misReservasProvider);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Mis reservas'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () =>
              context.canPop() ? context.pop() : context.go('/home'),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => ref.invalidate(misReservasProvider),
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 700,
        child: Column(
          children: [
            // Buscador + filtros
            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(12),
              child: Column(
                children: [
                  TextField(
                    onChanged: (v) => setState(() => _buscar = v),
                    decoration: InputDecoration(
                      hintText: 'Buscar por embarcación...',
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
                  if (kIsWeb)
                    // En web mostramos todos los chips juntos (con salto de
                    // línea si no caben) en vez de scroll horizontal, para
                    // que se vea toda la paleta de estados de una vez.
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: _estadoChips(),
                    )
                  else
                    SingleChildScrollView(
                      scrollDirection: Axis.horizontal,
                      child: Row(children: _estadoChips()),
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
                      const Text('Error al cargar reservas'),
                      const SizedBox(height: 8),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 24),
                        child: Text(
                          e.toString().replaceFirst('Exception: ', ''),
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                              fontSize: 12, color: AppTheme.textMuted),
                        ),
                      ),
                      const SizedBox(height: 12),
                      ElevatedButton(
                        onPressed: () => ref.invalidate(misReservasProvider),
                        child: const Text('Reintentar'),
                      ),
                    ],
                  ),
                ),
                data: (reservas) {
                  if (reservas.isEmpty) {
                    return EmptyStateWidget(
                      icon: Icons.book_online_outlined,
                      titulo: 'Sin reservas aún',
                      mensaje:
                          'No tienes ninguna reserva activa.\nHaz tu primera reserva ahora.',
                      botonLabel: 'Reservar embarcación',
                      onBoton: () => context.go('/embarcaciones'),
                    );
                  }

                  final filtradas = reservas.where((reserva) {
                    final embarcacion =
                        reserva['embarcacion'] as Map<String, dynamic>?;

                    final matchEstado = _estadoFiltro == 'todos' ||
                        reserva['estado'] == _estadoFiltro;

                    final matchBuscar = _buscar.isEmpty ||
                        (embarcacion?['nombre'] as String? ?? '')
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
                      icon: Icons.search_off,
                      titulo: 'Sin resultados',
                      mensaje:
                          'No hay reservas que coincidan\ncon los filtros aplicados.',
                    );
                  }

                  return RefreshIndicator(
                    onRefresh: () async => ref.invalidate(misReservasProvider),
                    child: ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: filtradas.length,
                      itemBuilder: (_, i) =>
                          _ReservaCard(reserva: filtradas[i]),
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

  @override
  Widget build(BuildContext context) {
    final estado = reserva['estado'] as String? ?? '';
    final fecha = reserva['fecha'] as String? ?? '';
    final embarcacion = reserva['embarcacion'] as Map<String, dynamic>?;
    final boleto = reserva['boleto'] as Map<String, dynamic>?;
    final pasajeros = reserva['total_personas'] as int? ?? 0;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        borderRadius: BorderRadius.circular(12),
        onTap: () => context.push('/reservas/${reserva['id']}'),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          '#${(reserva['id'] as int? ?? 0).toString().padLeft(6, '0')}',
                          style: const TextStyle(
                            fontSize: 12,
                            color: AppTheme.textMuted,
                            fontFamily: 'monospace',
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          embarcacion?['nombre'] ?? '',
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                            color: AppTheme.primary,
                          ),
                        ),
                      ],
                    ),
                  ),
                  _EstadoChip(estado: estado),
                ],
              ),
              const SizedBox(height: 12),
              const Divider(height: 1),
              const SizedBox(height: 12),
              Row(
                children: [
                  Expanded(
                    child: _InfoItem(
                      icon: Icons.calendar_today,
                      label: 'Fecha',
                      value: DateFormat('dd/MM/yyyy')
                          .format(DateTime.parse(fecha)),
                    ),
                  ),
                  if (reserva['hora_inicio'] != null)
                    Expanded(
                      child: _InfoItem(
                        icon: Icons.access_time,
                        label: 'Horario',
                        value:
                            '${reserva['hora_inicio']} - ${reserva['hora_fin']}',
                      ),
                    ),
                  Expanded(
                    child: _InfoItem(
                      icon: Icons.people_outline,
                      label: 'Pasajeros',
                      value: '$pasajeros',
                    ),
                  ),
                  if (boleto != null)
                    Expanded(
                      child: _InfoItem(
                        icon: Icons.qr_code,
                        label: 'Boleto',
                        value: estadoBoletoLabel(boleto['estado'] ?? ''),
                      ),
                    ),
                ],
              ),
              if ((estado == 'confirmada' || estado == 'completada') &&
                  boleto != null) ...[
                const SizedBox(height: 12),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: () =>
                            context.push('/boletos/${boleto['id']}'),
                        icon: const Icon(Icons.qr_code, size: 16),
                        label: const Text('Ver boleto'),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: AppTheme.primary,
                          side: const BorderSide(color: AppTheme.primary),
                        ),
                      ),
                    ),
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

class _InfoItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _InfoItem({
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
            Icon(icon, size: 12, color: AppTheme.textMuted),
            const SizedBox(width: 4),
            Text(
              label,
              style: const TextStyle(
                fontSize: 11,
                color: AppTheme.textMuted,
              ),
            ),
          ],
        ),
        const SizedBox(height: 2),
        Text(
          value,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: AppTheme.primary,
          ),
        ),
      ],
    );
  }
}

class _EstadoChip extends StatelessWidget {
  final String estado;
  const _EstadoChip({required this.estado});

  Color get _color {
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

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: _color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: _color),
      ),
      child: Text(
        estado.toUpperCase(),
        style: TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.w700,
          color: _color,
        ),
      ),
    );
  }
}
