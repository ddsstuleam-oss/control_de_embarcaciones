import 'dart:typed_data';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../providers/boleto_provider.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/file_saver.dart';
import '../../../../core/widgets/web_centered.dart';

class BoletoScreen extends ConsumerWidget {
  final int id;
  const BoletoScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final boletoAsync = ref.watch(boletoProvider(id));

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Boleto de embarque'),
        actions: [
          // ← Agregar botón refresh
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => ref.invalidate(boletoProvider(id)),
          ),
          boletoAsync.when(
            data: (b) => IconButton(
              icon: const Icon(Icons.picture_as_pdf),
              tooltip: 'Descargar PDF',
              onPressed: () => _descargarPDF(context, id),
            ),
            loading: () => const SizedBox.shrink(),
            error: (_, __) => const SizedBox.shrink(),
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 420,
        child: boletoAsync.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (e, _) => Center(child: Text('Error: $e')),
          data: (boleto) => _BoletoBody(boleto: boleto),
        ),
      ),
    );
  }

  Future<void> _descargarPDF(BuildContext context, int id) async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Generando PDF...')),
      );

      final response = await ApiClient().dio.get(
            ApiEndpoints.boletoPdf(id),
            options: Options(responseType: ResponseType.bytes),
          );

      await guardarYAbrirBytes(response.data as Uint8List, 'boleto_$id.pdf');
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error al descargar PDF: $e'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }
}

class _BoletoBody extends StatelessWidget {
  final Map<String, dynamic> boleto;
  const _BoletoBody({required this.boleto});

  @override
  Widget build(BuildContext context) {
    final codigoQr = boleto['codigo_qr'] as String? ?? '';
    final estado = boleto['estado'] as String? ?? '';
    final usadoEn = boleto['usado_en'] as String?;
    final reserva = boleto['reserva'] as Map<String, dynamic>?;
    final embarcacion = reserva?['embarcacion'] as Map<String, dynamic>?;
    final pasajeros = reserva?['pasajeros'] as List<dynamic>? ?? [];
    final fecha = reserva?['fecha'] as String? ?? '';

    final bool valido = estado == 'valido';

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          // Card del boleto
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.08),
                  blurRadius: 16,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: Column(
              children: [
                // Header
                Container(
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    color: AppTheme.primary,
                    borderRadius: BorderRadius.only(
                      topLeft: Radius.circular(16),
                      topRight: Radius.circular(16),
                    ),
                  ),
                  child: Row(
                    children: [
                      const Icon(
                        Icons.directions_boat_rounded,
                        color: Colors.white,
                        size: 28,
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'FCVT — ULEAM',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                                letterSpacing: 1,
                              ),
                            ),
                            Text(
                              embarcacion?['nombre'] ?? '',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 18,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                      ),
                      _EstadoBadge(estado: estado),
                    ],
                  ),
                ),

                // Banda roja
                Container(height: 4, color: AppTheme.accent),

                // Info
                Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: _InfoItem(
                              label: 'Fecha',
                              value: fecha.isNotEmpty
                                  ? DateFormat('dd/MM/yyyy')
                                      .format(DateTime.parse(fecha))
                                  : '',
                              icon: Icons.calendar_today,
                            ),
                          ),
                          Expanded(
                            child: _InfoItem(
                              label: 'Pasajeros',
                              value: '${reserva?['total_personas'] ?? 0}',
                              icon: Icons.people_outline,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      _InfoItem(
                        label: 'Horario',
                        value: (reserva?['hora_inicio'] != null &&
                                reserva?['hora_fin'] != null)
                            ? '${(reserva!['hora_inicio'] as String).substring(0, 5)} — ${(reserva['hora_fin'] as String).substring(0, 5)}'
                            : '—',
                        icon: Icons.access_time_rounded,
                      ),
                      const SizedBox(height: 16),

                      // Pasajeros
                      if (pasajeros.isNotEmpty) ...[
                        const Divider(),
                        const SizedBox(height: 8),
                        const Align(
                          alignment: Alignment.centerLeft,
                          child: Text(
                            'PASAJEROS',
                            style: TextStyle(
                              fontSize: 10,
                              color: AppTheme.textMuted,
                              letterSpacing: 1.5,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        ...pasajeros.asMap().entries.map((e) {
                          final p = e.value as Map<String, dynamic>;
                          return Padding(
                            padding: const EdgeInsets.symmetric(vertical: 3),
                            child: Row(
                              children: [
                                Text(
                                  '${e.key + 1}. ',
                                  style: const TextStyle(
                                    color: AppTheme.accent,
                                    fontWeight: FontWeight.w700,
                                    fontSize: 13,
                                  ),
                                ),
                                Expanded(
                                  child: Text(
                                    p['nombre'] ?? '',
                                    style: const TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                                ),
                                Text(
                                  p['cedula'] ?? '',
                                  style: const TextStyle(
                                    fontSize: 12,
                                    color: AppTheme.textMuted,
                                    fontFamily: 'monospace',
                                  ),
                                ),
                              ],
                            ),
                          );
                        }),
                      ],

                      // Tear line
                      const SizedBox(height: 16),
                      Row(
                        children: List.generate(
                          30,
                          (_) => Expanded(
                            child: Container(
                              height: 1,
                              color: Colors.grey[300],
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // QR
                      if (valido) ...[
                        const Text(
                          'Presenta este código al embarcar',
                          style: TextStyle(
                            fontSize: 12,
                            color: AppTheme.textMuted,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: Colors.grey[200]!,
                              width: 2,
                            ),
                          ),
                          child: QrImageView(
                            data: codigoQr,
                            version: QrVersions.auto,
                            size: 200,
                            backgroundColor: Colors.white,
                            eyeStyle: const QrEyeStyle(
                              eyeShape: QrEyeShape.square,
                              color: AppTheme.primary,
                            ),
                            dataModuleStyle: const QrDataModuleStyle(
                              dataModuleShape: QrDataModuleShape.square,
                              color: AppTheme.primary,
                            ),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          codigoQr,
                          style: const TextStyle(
                            fontSize: 11,
                            color: AppTheme.textMuted,
                            fontFamily: 'monospace',
                            letterSpacing: 2,
                          ),
                        ),
                      ] else ...[
                        Icon(
                          estado == 'usado'
                              ? Icons.check_circle
                              : estado == 'vencido'
                                  ? Icons.timer_off
                                  : Icons.cancel,
                          size: 64,
                          color: estado == 'usado'
                              ? AppTheme.success
                              : estado == 'vencido'
                                  ? AppTheme.textMuted
                                  : AppTheme.error,
                        ),
                        const SizedBox(height: 8),
                        Text(
                          estado == 'usado'
                              ? 'Boleto utilizado el $usadoEn'
                              : estado == 'vencido'
                                  ? 'Venció sin ser utilizado'
                                  : 'Boleto cancelado',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w600,
                            color: estado == 'usado'
                                ? AppTheme.success
                                : estado == 'vencido'
                                    ? AppTheme.textMuted
                                    : AppTheme.error,
                          ),
                        ),
                      ],
                    ],
                  ),
                ),

                // Footer
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppTheme.accent,
                    borderRadius: const BorderRadius.only(
                      bottomLeft: Radius.circular(16),
                      bottomRight: Radius.circular(16),
                    ),
                  ),
                  child: const Text(
                    'Válido únicamente para la fecha indicada · ULEAM',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 10,
                      letterSpacing: 0.5,
                    ),
                  ),
                ),
              ],
            ),
          ),

          const SizedBox(height: 16),

          // Botón descargar PDF
          OutlinedButton.icon(
            onPressed: () => _BoletoScreen_descargarPDF(context, boleto['id']),
            icon: const Icon(Icons.picture_as_pdf, color: AppTheme.accent),
            label: const Text(
              'Descargar PDF',
              style: TextStyle(color: AppTheme.accent),
            ),
            style: OutlinedButton.styleFrom(
              minimumSize: const Size(double.infinity, 48),
              side: const BorderSide(color: AppTheme.accent),
            ),
          ),

          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Future<void> _BoletoScreen_descargarPDF(BuildContext context, int id) async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Generando PDF...')),
      );

      final response = await ApiClient().dio.get(
            ApiEndpoints.boletoPdf(id),
            options: Options(responseType: ResponseType.bytes),
          );

      await guardarYAbrirBytes(response.data as Uint8List, 'boleto_$id.pdf');
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Error: $e'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }
}

class _InfoItem extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;

  const _InfoItem({
    required this.label,
    required this.value,
    required this.icon,
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
            fontSize: 15,
            fontWeight: FontWeight.w700,
            color: AppTheme.primary,
          ),
        ),
      ],
    );
  }
}

class _EstadoBadge extends StatelessWidget {
  final String estado;
  const _EstadoBadge({required this.estado});

  @override
  Widget build(BuildContext context) {
    final color = estado == 'valido'
        ? AppTheme.success
        : estado == 'usado'
            ? AppTheme.warning
            : estado == 'vencido'
                ? AppTheme.textMuted
                : AppTheme.error;

    final label = estado == 'vencido' ? 'VENCIDO' : estado.toUpperCase();

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.2),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: color,
          fontSize: 10,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}
