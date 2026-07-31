import 'dart:typed_data';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../../core/api/api_client.dart';
import '../../../../../core/api/api_endpoints.dart';
import '../../../../../core/theme/app_theme.dart';
import '../../../../../core/utils/file_saver.dart';
import '../../../../../core/widgets/admin_nav.dart';
import '../../../../../core/widgets/admin_page_scaffold.dart';

class ReportesScreen extends ConsumerStatefulWidget {
  const ReportesScreen({super.key});

  @override
  ConsumerState<ReportesScreen> createState() => _ReportesScreenState();
}

class _ReportesScreenState extends ConsumerState<ReportesScreen> {
  DateTime _fecha      = DateTime.now();
  DateTime _desde      = DateTime.now().subtract(const Duration(days: 30));
  DateTime _hasta      = DateTime.now();
  int?     _embarcacionId;
  bool     _isLoading  = false;

  String get _fechaStr  => DateFormat('yyyy-MM-dd').format(_fecha);
  String get _desdeStr  => DateFormat('yyyy-MM-dd').format(_desde);
  String get _hastaStr  => DateFormat('yyyy-MM-dd').format(_hasta);

  Future<void> _seleccionarFecha({
    required bool esFecha,
    required bool esDesde,
  }) async {
    final initial = esFecha ? _fecha : (esDesde ? _desde : _hasta);
    final picked  = await showDatePicker(
      context:     context,
      initialDate: initial,
      firstDate:   DateTime(2024),
      lastDate:    DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: const ColorScheme.light(primary: AppTheme.primary),
        ),
        child: child!,
      ),
    );

    if (picked != null) {
      setState(() {
        if (esFecha) {
          _fecha = picked;
        } else if (esDesde) {
          _desde = picked;
        } else {
          _hasta = picked;
        }
      });
    }
  }

  Future<void> _descargar({
    required String url,
    required Map<String, dynamic> params,
    required String filename,
    required String extension,
  }) async {
    setState(() => _isLoading = true);

    try {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Generando $filename...'),
          duration: const Duration(seconds: 2),
        ),
      );

      final response = await ApiClient().dio.get(
        url,
        queryParameters: params,
        options: Options(responseType: ResponseType.bytes),
      );

      if (response.statusCode != 200) {
        throw Exception('Error al generar el reporte');
      }

      await guardarYAbrirBytes(
        response.data as Uint8List,
        '$filename.$extension',
      );
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content:         Text('Error: ${e.toString().replaceAll('Exception: ', '')}'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AdminPageScaffold(
      navItem: AdminNavItem.reportes,
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [

                // ── REPORTE POR FECHA ─────────────────────────────────
                _SectionCard(
                  title:    'Reporte por fecha',
                  subtitle: 'Pasajeros y manifiesto de embarque',
                  icon:     Icons.calendar_today,
                  color:    AppTheme.primary,
                  child: Column(
                    children: [

                      // Selector fecha
                      _FechaSelector(
                        label: 'Fecha de embarque',
                        fecha: _fecha,
                        onTap: () => _seleccionarFecha(
                          esFecha: true,
                          esDesde: false,
                        ),
                      ),

                      const SizedBox(height: 16),

                      // Botones
                      Row(
                        children: [
                          Expanded(
                            child: _ReporteButton(
                              label:    'Excel pasajeros',
                              icon:     Icons.table_chart,
                              color:    const Color(0xFF1D6F42),
                              onPressed: _isLoading
                                  ? null
                                  : () => _descargar(
                                      url:       ApiEndpoints.reporteExcelPasajeros,
                                      params:    {
                                        'fecha': _fechaStr,
                                        if (_embarcacionId != null)
                                          'embarcacion_id': _embarcacionId,
                                      },
                                      filename:  'pasajeros-$_fechaStr',
                                      extension: 'xlsx',
                                    ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _ReporteButton(
                              label:    'PDF manifiesto',
                              icon:     Icons.picture_as_pdf,
                              color:    AppTheme.error,
                              onPressed: _isLoading
                                  ? null
                                  : () => _descargar(
                                      url:       ApiEndpoints.reportePdfManifiesto,
                                      params:    {
                                        'fecha': _fechaStr,
                                        if (_embarcacionId != null)
                                          'embarcacion_id': _embarcacionId,
                                      },
                                      filename:  'manifiesto-$_fechaStr',
                                      extension: 'pdf',
                                    ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 16),

                // ── REPORTE POR RANGO ─────────────────────────────────
                _SectionCard(
                  title:    'Reporte por rango',
                  subtitle: 'Reservas en un período de tiempo',
                  icon:     Icons.date_range,
                  color:    AppTheme.info,
                  child: Column(
                    children: [

                      // Desde
                      _FechaSelector(
                        label: 'Desde',
                        fecha: _desde,
                        onTap: () => _seleccionarFecha(
                          esFecha: false,
                          esDesde: true,
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Hasta
                      _FechaSelector(
                        label: 'Hasta',
                        fecha: _hasta,
                        onTap: () => _seleccionarFecha(
                          esFecha: false,
                          esDesde: false,
                        ),
                      ),
                      const SizedBox(height: 16),

                      _ReporteButton(
                        label:    'Descargar Excel de reservas',
                        icon:     Icons.table_chart,
                        color:    const Color(0xFF1D6F42),
                        onPressed: _isLoading
                            ? null
                            : () => _descargar(
                                url:       ApiEndpoints.reporteExcelReservas,
                                params:    {
                                  'desde': _desdeStr,
                                  'hasta': _hastaStr,
                                  if (_embarcacionId != null)
                                    'embarcacion_id': _embarcacionId,
                                },
                                filename:  'reservas-$_desdeStr-al-$_hastaStr',
                                extension: 'xlsx',
                              ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 32),
              ],
            ),
          ),

          // Loading overlay
          if (_isLoading)
            Container(
              color: Colors.black.withOpacity(0.3),
              child: const Center(
                child: Card(
                  child: Padding(
                    padding: EdgeInsets.all(24),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        CircularProgressIndicator(color: AppTheme.primary),
                        SizedBox(height: 16),
                        Text('Generando reporte...'),
                      ],
                    ),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}

class _SectionCard extends StatelessWidget {
  final String  title;
  final String  subtitle;
  final IconData icon;
  final Color   color;
  final Widget  child;

  const _SectionCard({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.color,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color:        Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color:     Colors.black.withOpacity(0.06),
            blurRadius: 12,
            offset:    const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color:        color.withOpacity(0.08),
              borderRadius: const BorderRadius.only(
                topLeft:  Radius.circular(16),
                topRight: Radius.circular(16),
              ),
            ),
            child: Row(
              children: [
                Container(
                  padding:    const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color:        color.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Icon(icon, color: color, size: 20),
                ),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: TextStyle(
                        fontSize:   15,
                        fontWeight: FontWeight.w700,
                        color:      color,
                      ),
                    ),
                    Text(
                      subtitle,
                      style: const TextStyle(
                        fontSize: 12,
                        color:    AppTheme.textMuted,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16),
            child:   child,
          ),
        ],
      ),
    );
  }
}

class _FechaSelector extends StatelessWidget {
  final String   label;
  final DateTime fecha;
  final VoidCallback onTap;

  const _FechaSelector({
    required this.label,
    required this.fecha,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap:        onTap,
      borderRadius: BorderRadius.circular(10),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          color:        Colors.grey[100],
          borderRadius: BorderRadius.circular(10),
          border:       Border.all(color: Colors.grey[300]!),
        ),
        child: Row(
          children: [
            const Icon(Icons.calendar_today,
                color: AppTheme.primary, size: 18),
            const SizedBox(width: 12),
            Flexible(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    label,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      fontSize: 11,
                      color:    AppTheme.textMuted,
                    ),
                  ),
                  Text(
                    DateFormat('EEEE, d MMMM yyyy', 'es').format(fecha),
                    overflow: TextOverflow.ellipsis,
                    maxLines: 1,
                    style: const TextStyle(
                      fontSize:   14,
                      fontWeight: FontWeight.w600,
                      color:      AppTheme.primary,
                    ),
                  ),
                ],
              ),
            ),
            const Spacer(),
            const Icon(Icons.chevron_right, color: AppTheme.textMuted),
          ],
        ),
      ),
    );
  }
}

class _ReporteButton extends StatelessWidget {
  final String       label;
  final IconData     icon;
  final Color        color;
  final VoidCallback? onPressed;

  const _ReporteButton({
    required this.label,
    required this.icon,
    required this.color,
    this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return ElevatedButton.icon(
      onPressed: onPressed,
      icon:  Icon(icon, size: 18),
      label: Text(label),
      style: ElevatedButton.styleFrom(
        backgroundColor: color,
        foregroundColor: Colors.white,
        minimumSize:     const Size(double.infinity, 48),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(10),
        ),
      ),
    );
  }
}