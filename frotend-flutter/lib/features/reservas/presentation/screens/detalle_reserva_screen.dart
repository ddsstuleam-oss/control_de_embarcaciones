import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../providers/reserva_provider.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/boleto_utils.dart';
import '../../../../core/widgets/web_centered.dart';

class DetalleReservaScreen extends ConsumerWidget {
  final int id;
  const DetalleReservaScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final reservaAsync = ref.watch(reservaDetalleProvider(id));

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: Text('Reserva #${id.toString().padLeft(6, '0')}'),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () =>
              context.canPop() ? context.pop() : context.go('/reservas'),
        ),
      ),
      body: WebCentered(
        maxWidth: 560,
        child: reservaAsync.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (e, _) => Center(child: Text('Error: $e')),
          data: (reserva) => _DetalleBody(reserva: reserva, ref: ref),
        ),
      ),
    );
  }
}

class _DetalleBody extends StatelessWidget {
  final Map<String, dynamic> reserva;
  final WidgetRef ref;

  const _DetalleBody({required this.reserva, required this.ref});

  @override
  Widget build(BuildContext context) {
    final estado = reserva['estado'] as String? ?? '';
    final fecha = reserva['fecha'] as String? ?? '';
    final embarcacion = reserva['embarcacion'] as Map<String, dynamic>?;
    final pasajeros = reserva['pasajeros'] as List<dynamic>? ?? [];
    final boleto = reserva['boleto'] as Map<String, dynamic>?;
    final creadoEn = reserva['creado_en'] as String? ?? '';
    final decididoPor = reserva['decidido_por'] as String?;
    final canceladoPor = reserva['cancelado_por'] as String?;

    return Scrollbar(
      thumbVisibility: true,
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Estado
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppTheme.primary,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(
                children: [
                  Text(
                    embarcacion?['nombre'] ?? '',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 20,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 8),
                  _EstadoBadge(estado: estado),
                  const SizedBox(height: 8),
                  Text(
                    'Reservado el $creadoEn',
                    style: const TextStyle(
                      color: Colors.white60,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Info general
            _Card(
              title: 'Información del viaje',
              child: Column(
                children: [
                  _Row(
                      label: 'Fecha',
                      value: DateFormat('EEEE, d MMMM yyyy', 'es')
                          .format(DateTime.parse(fecha))),
                  _Row(
                    label: 'Horario',
                    value: reserva['hora_inicio'] != null &&
                            reserva['hora_fin'] != null
                        ? '${reserva['hora_inicio']} — ${reserva['hora_fin']}'
                        : 'No especificado',
                  ),
                  _Row(
                      label: 'Embarcación',
                      value: embarcacion?['nombre'] ?? ''),
                  _Row(
                      label: 'Capacidad',
                      value: '${embarcacion?['capacidad'] ?? 0} pasajeros'),
                  _Row(
                      label: 'Total reservados',
                      value: '${reserva['total_personas']} pasajeros'),
                  if (decididoPor != null)
                    _Row(
                      label: estado == 'rechazada'
                          ? 'Rechazado por'
                          : 'Aprobado por',
                      value: decididoPor,
                    ),
                  if (canceladoPor != null)
                    _Row(label: 'Cancelado por', value: canceladoPor),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // Pasajeros
            _Card(
              title: 'Pasajeros (${pasajeros.length})',
              child: Column(
                children: pasajeros.asMap().entries.map((e) {
                  final i = e.key;
                  final p = e.value as Map<String, dynamic>;
                  final embarqueConfirmado =
                      boleto != null && boleto['estado'] == 'usado';
                  final presente = p['presente'] as bool? ?? true;
                  return Container(
                    padding: const EdgeInsets.symmetric(vertical: 8),
                    decoration: BoxDecoration(
                      border: Border(
                        bottom: i < pasajeros.length - 1
                            ? BorderSide(color: Colors.grey[200]!)
                            : BorderSide.none,
                      ),
                    ),
                    child: Row(
                      children: [
                        Container(
                          width: 28,
                          height: 28,
                          decoration: BoxDecoration(
                            color: AppTheme.primary.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(14),
                          ),
                          child: Center(
                            child: Text(
                              '${i + 1}',
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w700,
                                color: AppTheme.primary,
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                p['nombre'] ?? '',
                                style: const TextStyle(
                                  fontWeight: FontWeight.w600,
                                  fontSize: 14,
                                ),
                              ),
                              Text(
                                'CI: ${p['cedula']} · ${(p['tipo'] ?? 'externo').toString().toUpperCase()}',
                                style: const TextStyle(
                                  fontSize: 12,
                                  color: AppTheme.textMuted,
                                ),
                              ),
                            ],
                          ),
                        ),
                        if (embarqueConfirmado)
                          Icon(
                            presente ? Icons.check_circle : Icons.cancel,
                            size: 18,
                            color: presente ? AppTheme.success : AppTheme.error,
                          ),
                      ],
                    ),
                  );
                }).toList(),
              ),
            ),

            const SizedBox(height: 16),

            // Boleto
            if (boleto != null) ...[
              _Card(
                title: 'Boleto de embarque',
                child: Column(
                  children: [
                    _Row(label: 'Código QR', value: boleto['codigo_qr'] ?? ''),
                    _Row(
                        label: 'Estado',
                        value: estadoBoletoLabel(boleto['estado'] ?? '')),
                    const SizedBox(height: 12),
                    ElevatedButton.icon(
                      onPressed: () => context.push('/boletos/${boleto['id']}'),
                      icon: const Icon(Icons.qr_code),
                      label: const Text('Ver boleto QR'),
                      style: ElevatedButton.styleFrom(
                        minimumSize: const Size(double.infinity, 48),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
            ],

            // Cancelar
            if (estado == 'confirmada' || estado == 'pendiente') ...[
              OutlinedButton.icon(
                onPressed: () => _confirmarCancelar(context, ref),
                icon: const Icon(Icons.cancel_outlined, color: AppTheme.error),
                label: const Text('Cancelar reserva',
                    style: TextStyle(color: AppTheme.error)),
                style: OutlinedButton.styleFrom(
                  minimumSize: const Size(double.infinity, 48),
                  side: const BorderSide(color: AppTheme.error),
                ),
              ),
              const SizedBox(height: 32),
            ],
          ],
        ),
      ),
    );
  }

  Future<void> _confirmarCancelar(BuildContext context, WidgetRef ref) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Cancelar reserva'),
        content: const Text(
            '¿Estás seguro de cancelar esta reserva? Esta acción no se puede deshacer.'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('No'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Sí, cancelar',
                style: TextStyle(color: AppTheme.error)),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    try {
      await ref.read(reservaRepositoryProvider).cancelarReserva(reserva['id']);
      ref.invalidate(misReservasProvider);
      ref.invalidate(reservaDetalleProvider(reserva['id']));
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Reserva cancelada'),
            backgroundColor: AppTheme.success,
          ),
        );
        context.pop();
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(e.toString().replaceAll('Exception: ', '')),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }
}

class _EstadoBadge extends StatelessWidget {
  final String estado;
  const _EstadoBadge({required this.estado});

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
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      decoration: BoxDecoration(
        color: _color.withOpacity(0.2),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: _color),
      ),
      child: Text(
        estado.toUpperCase(),
        style: TextStyle(
          color: _color,
          fontWeight: FontWeight.w700,
          fontSize: 13,
        ),
      ),
    );
  }
}

class _Card extends StatelessWidget {
  final String title;
  final Widget child;

  const _Card({required this.title, required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
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
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w700,
              color: AppTheme.primary,
            ),
          ),
          const SizedBox(height: 12),
          child,
        ],
      ),
    );
  }
}

class _Row extends StatelessWidget {
  final String label;
  final String value;

  const _Row({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              label,
              style: const TextStyle(
                fontSize: 13,
                color: AppTheme.textMuted,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: AppTheme.primary,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
