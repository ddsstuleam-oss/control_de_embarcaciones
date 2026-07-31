import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../theme/app_theme.dart';
import '../utils/file_saver.dart';
import '../../features/viajes/data/viaje_repository.dart';

/// Hoja de detalle de una reserva: datos del viaje, responsable, pasajeros y
/// boleto. Los botones de acción (aprobar/rechazar/cancelar) son opcionales —
/// si no se pasan, la hoja se muestra en modo solo lectura (p. ej. para el
/// operador, que no tiene permisos para esas acciones).
class ReservaDetalleSheet extends StatelessWidget {
  final Map<String, dynamic> reserva;
  final VoidCallback?        onAprobar;
  final VoidCallback?        onRechazar;
  final VoidCallback?        onCancelar;

  const ReservaDetalleSheet({
    super.key,
    required this.reserva,
    this.onAprobar,
    this.onRechazar,
    this.onCancelar,
  });

  Color _estadoColor(String estado) {
    switch (estado) {
      case 'confirmada':  return AppTheme.success;
      case 'pendiente':   return AppTheme.warning;
      case 'rechazada':   return AppTheme.error;
      case 'cancelada':   return AppTheme.error;
      case 'completada':  return AppTheme.info;
      case 'vencida':     return AppTheme.textMuted;
      default:            return AppTheme.warning;
    }
  }

  Future<void> _descargarReporte(BuildContext context, int viajeId) async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Generando reporte...')),
      );
      final bytes = await ViajeRepository().descargarReportePdfBytes(viajeId);
      await guardarYAbrirBytes(bytes, 'reporte_viaje_$viajeId.pdf');
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content:         Text('Error al generar el reporte: $e'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final embarcacion = reserva['embarcacion'] as Map<String, dynamic>?;
    final usuario     = reserva['user']        as Map<String, dynamic>?;
    final pasajeros    = reserva['pasajeros']  as List<dynamic>? ?? [];
    final boleto       = reserva['boleto']     as Map<String, dynamic>?;
    final viaje        = reserva['viaje']      as Map<String, dynamic>?;
    final fecha        = reserva['fecha']      as String? ?? '';
    final estado       = reserva['estado']     as String? ?? '';
    final personas     = reserva['total_personas'] as int? ?? 0;
    final color        = _estadoColor(estado);

    return DraggableScrollableSheet(
      initialChildSize: 0.75,
      minChildSize:      0.4,
      maxChildSize:      0.95,
      expand: false,
      builder: (_, scrollController) => Container(
        decoration: const BoxDecoration(
          color:        Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: SafeArea(
          top: false,
          child: Column(
          children: [
            const SizedBox(height: 10),
            Container(
              width:  40,
              height: 4,
              decoration: BoxDecoration(
                color:        Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            Expanded(
              child: Scrollbar(
                controller: scrollController,
                thumbVisibility: true,
                child: ListView(
                controller: scrollController,
                padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
                children: [
                  Row(
                    children: [
                      Text(
                        '#${(reserva['id'] as int).toString().padLeft(6, '0')}',
                        style: const TextStyle(
                          fontSize:   12,
                          color:      AppTheme.textMuted,
                          fontFamily: 'monospace',
                        ),
                      ),
                      const Spacer(),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color:        color.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                          border:       Border.all(color: color),
                        ),
                        child: Text(
                          estado.toUpperCase(),
                          style: TextStyle(
                            fontSize:   11,
                            fontWeight: FontWeight.w700,
                            color:      color,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text(
                    embarcacion?['nombre'] ?? '',
                    style: const TextStyle(
                      fontSize:   20,
                      fontWeight: FontWeight.w800,
                      color:      AppTheme.primary,
                    ),
                  ),
                  const SizedBox(height: 16),

                  const _SeccionTitulo('Datos del viaje'),
                  const SizedBox(height: 8),
                  _DetalleFila(
                    icon:  Icons.calendar_today,
                    label: 'Fecha',
                    value: fecha.isNotEmpty
                        ? DateFormat('EEEE, d MMMM yyyy', 'es')
                            .format(DateTime.parse(fecha))
                        : '',
                  ),
                  if (reserva['hora_inicio'] != null)
                    _DetalleFila(
                      icon:  Icons.access_time,
                      label: 'Horario',
                      value: '${reserva['hora_inicio']} — ${reserva['hora_fin']}',
                    ),
                  _DetalleFila(
                    icon:  Icons.people_outline,
                    label: 'Pasajeros',
                    value: '$personas',
                  ),
                  if (embarcacion?['capacidad'] != null)
                    _DetalleFila(
                      icon:  Icons.directions_boat,
                      label: 'Capacidad embarcación',
                      value: '${embarcacion?['capacidad']} personas',
                    ),
                  if (reserva['creado_en'] != null)
                    _DetalleFila(
                      icon:  Icons.event_available,
                      label: 'Reservado el',
                      value: '${reserva['creado_en']}',
                    ),
                  if (reserva['decidido_por'] != null)
                    _DetalleFila(
                      icon:  Icons.verified_user_outlined,
                      label: estado == 'rechazada' ? 'Rechazado por' : 'Aprobado por',
                      value: reserva['decidido_por'],
                    ),
                  if (reserva['cancelado_por'] != null)
                    _DetalleFila(
                      icon:  Icons.cancel_outlined,
                      label: 'Cancelado por',
                      value: reserva['cancelado_por'],
                    ),

                  const SizedBox(height: 20),
                  const _SeccionTitulo('Reservado por'),
                  const SizedBox(height: 8),
                  if (usuario != null) ...[
                    _DetalleFila(
                      icon:  Icons.person_outline,
                      label: 'Nombre',
                      value: usuario['nombre'] ?? '',
                    ),
                    _DetalleFila(
                      icon:  Icons.email_outlined,
                      label: 'Correo',
                      value: usuario['email'] ?? '',
                    ),
                    if (usuario['cedula'] != null)
                      _DetalleFila(
                        icon:  Icons.badge_outlined,
                        label: 'Cédula',
                        value: usuario['cedula'],
                      ),
                    _DetalleFila(
                      icon:  Icons.phone_outlined,
                      label: 'Teléfono',
                      value: (usuario['telefono'] as String?)?.isNotEmpty == true
                          ? usuario['telefono']
                          : 'No registrado',
                    ),
                  ] else
                    const Text(
                      'No disponible',
                      style: TextStyle(fontSize: 13, color: AppTheme.textMuted),
                    ),

                  const SizedBox(height: 20),
                  _SeccionTitulo('Pasajeros (${pasajeros.length})'),
                  const SizedBox(height: 8),
                  ...pasajeros.asMap().entries.map((e) {
                    final i = e.key;
                    final p = e.value as Map<String, dynamic>;
                    final embarqueConfirmado = boleto != null &&
                        boleto['estado'] == 'usado';
                    final presente = p['presente'] as bool? ?? true;
                    return Container(
                      margin: const EdgeInsets.only(bottom: 8),
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color:        Colors.grey[50],
                        borderRadius: BorderRadius.circular(10),
                        border:       Border.all(color: Colors.grey[200]!),
                      ),
                      child: Row(
                        children: [
                          Container(
                            width:  24,
                            height: 24,
                            decoration: BoxDecoration(
                              color: AppTheme.primary.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Center(
                              child: Text(
                                '${i + 1}',
                                style: const TextStyle(
                                  fontSize:   11,
                                  fontWeight: FontWeight.w700,
                                  color:      AppTheme.primary,
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 10),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  p['nombre'] ?? '',
                                  style: const TextStyle(
                                    fontWeight: FontWeight.w600,
                                    fontSize:   13,
                                  ),
                                ),
                                Text(
                                  'CI: ${p['cedula'] ?? ''} · '
                                  '${(p['tipo'] ?? 'externo').toString().toUpperCase()}'
                                  '${p['carrera'] != null ? ' · ${p['carrera']}' : ''}'
                                  '${p['facultad'] != null ? ' · ${p['facultad']}' : ''}',
                                  style: const TextStyle(
                                    fontSize: 11,
                                    color:    AppTheme.textMuted,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          if (embarqueConfirmado)
                            Icon(
                              presente
                                  ? Icons.check_circle
                                  : Icons.cancel,
                              size:  18,
                              color: presente
                                  ? AppTheme.success
                                  : AppTheme.error,
                            ),
                        ],
                      ),
                    );
                  }),

                  if (boleto != null) ...[
                    const SizedBox(height: 12),
                    const _SeccionTitulo('Boleto'),
                    const SizedBox(height: 8),
                    _DetalleFila(
                      icon:  Icons.qr_code,
                      label: 'Código QR',
                      value: boleto['codigo_qr'] ?? '',
                    ),
                    _DetalleFila(
                      icon:  Icons.verified_outlined,
                      label: 'Estado',
                      value: (boleto['estado'] ?? '').toString().toUpperCase(),
                    ),
                    if (boleto['estado'] == 'usado')
                      _DetalleFila(
                        icon:  Icons.groups_outlined,
                        label: 'Abordaron',
                        value:
                            '${pasajeros.where((p) => (p as Map<String, dynamic>)['presente'] as bool? ?? true).length}'
                            '/${pasajeros.length}',
                      ),
                    if (estado == 'completada' && viaje != null) ...[
                      const SizedBox(height: 12),
                      OutlinedButton.icon(
                        onPressed: () => _descargarReporte(context, viaje['id'] as int),
                        icon:  const Icon(Icons.picture_as_pdf_outlined, size: 18),
                        label: const Text('Generar reporte del viaje'),
                        style: OutlinedButton.styleFrom(
                          minimumSize:     const Size(double.infinity, 44),
                          foregroundColor: AppTheme.primary,
                          side: const BorderSide(color: AppTheme.primary),
                        ),
                      ),
                    ],
                  ],

                  const SizedBox(height: 12),
                ],
                ),
              ),
            ),

            if (onAprobar != null || onRechazar != null || onCancelar != null)
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                child: Row(
                  children: [
                    if (onAprobar != null)
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: () {
                            Navigator.pop(context);
                            onAprobar!();
                          },
                          icon:  const Icon(Icons.check_circle_outline, size: 18),
                          label: const Text('Aprobar'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppTheme.success,
                          ),
                        ),
                      ),
                    if (onAprobar != null && onRechazar != null)
                      const SizedBox(width: 8),
                    if (onRechazar != null)
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () {
                            Navigator.pop(context);
                            onRechazar!();
                          },
                          icon:  const Icon(Icons.highlight_off, size: 18),
                          label: const Text('Rechazar'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: AppTheme.error,
                            side: const BorderSide(color: AppTheme.error),
                          ),
                        ),
                      ),
                    if (onCancelar != null)
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () {
                            Navigator.pop(context);
                            onCancelar!();
                          },
                          icon:  const Icon(Icons.cancel_outlined, size: 18),
                          label: const Text('Cancelar'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: AppTheme.error,
                            side: const BorderSide(color: AppTheme.error),
                          ),
                        ),
                      ),
                  ],
                ),
              ),
          ],
          ),
        ),
      ),
    );
  }
}

class _SeccionTitulo extends StatelessWidget {
  final String texto;
  const _SeccionTitulo(this.texto);

  @override
  Widget build(BuildContext context) {
    return Text(
      texto,
      style: const TextStyle(
        fontSize:   13,
        fontWeight: FontWeight.w700,
        color:      AppTheme.primary,
        letterSpacing: 0.3,
      ),
    );
  }
}

class _DetalleFila extends StatelessWidget {
  final IconData icon;
  final String   label;
  final String   value;

  const _DetalleFila({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 5),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 15, color: AppTheme.textMuted),
          const SizedBox(width: 8),
          SizedBox(
            width: 130,
            child: Text(
              label,
              style: const TextStyle(fontSize: 12, color: AppTheme.textMuted),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                fontSize:   13,
                fontWeight: FontWeight.w600,
                color:      AppTheme.primary,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
