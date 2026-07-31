import 'dart:typed_data';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../../../../../core/api/api_client.dart';
import '../../../../../core/api/api_endpoints.dart';
import '../../../../../core/theme/app_theme.dart';
import '../../../../../core/utils/file_saver.dart';
import '../../../../../core/widgets/admin_nav.dart';
import '../../../../../core/widgets/admin_page_scaffold.dart';
import '../../../../../core/widgets/empty_state_widget.dart';

final directorioProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.adminDirectorio);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar directorio');
});

class DirectorioScreen extends ConsumerStatefulWidget {
  const DirectorioScreen({super.key});

  @override
  ConsumerState<DirectorioScreen> createState() => _DirectorioScreenState();
}

class _DirectorioScreenState extends ConsumerState<DirectorioScreen> {
  bool _importando = false;

  Future<void> _importarExcel() async {
    final picker = ImagePicker();
    // Usamos XFile para seleccionar archivos
    final result = await picker.pickMedia();

    if (result == null) return;

    setState(() => _importando = true);

    try {
      final formData = FormData.fromMap({
        'archivo': MultipartFile.fromBytes(
          await result.readAsBytes(),
          filename: result.name,
        ),
      });

      final response = await ApiClient().dio.post(
        ApiEndpoints.adminDirectorioImportar,
        data: formData,
      );

      ref.invalidate(directorioProvider);

      if (mounted) {
        final data = response.data;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'Importados: ${data['importados']} · '
              'Actualizados: ${data['actualizados']} · '
              'Errores: ${(data['errores'] as List).length}',
            ),
            backgroundColor: AppTheme.success,
            duration: const Duration(seconds: 4),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content:         Text('Error: $e'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _importando = false);
    }
  }

  Future<void> _descargarPlantilla() async {
    try {
      final response = await ApiClient().dio.get(
        ApiEndpoints.adminDirectorioPlantilla,
        options: Options(responseType: ResponseType.bytes),
      );

      await guardarYAbrirBytes(
        response.data as Uint8List,
        'plantilla_directorio.xlsx',
      );
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content:         Text('Error: $e'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final directorioAsync = ref.watch(directorioProvider);

    return AdminPageScaffold(
      navItem: AdminNavItem.directorio,
      title: 'Directorio de personas',
      actions: [
        IconButton(
          icon:      const Icon(Icons.refresh),
          onPressed: () => ref.invalidate(directorioProvider),
        ),
      ],
      body: Column(
        children: [

          // Acciones
          Container(
            color:   Colors.white,
            padding: const EdgeInsets.all(12),
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: ElevatedButton.icon(
                        onPressed: _importando ? null : _importarExcel,
                        icon: _importando
                            ? const SizedBox(
                                width: 16, height: 16,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2, color: Colors.white,
                                ),
                              )
                            : const Icon(Icons.upload_file, size: 18),
                        label: Text(
                          _importando ? 'Importando...' : 'Importar Excel',
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF1D6F42),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: OutlinedButton.icon(
                        onPressed: _descargarPlantilla,
                        icon:  const Icon(Icons.download, size: 18),
                        label: const Text('Descargar plantilla'),
                        style: OutlinedButton.styleFrom(
                          foregroundColor: AppTheme.primary,
                          side: const BorderSide(color: AppTheme.primary),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color:        AppTheme.info.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Row(
                    children: [
                      Icon(Icons.info_outline,
                          color: AppTheme.info, size: 16),
                      SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'Sube un Excel con columnas: cedula, nombre, tipo, carrera, facultad, telefono, email',
                          style: TextStyle(
                            fontSize: 11,
                            color:    AppTheme.info,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // Lista
          Expanded(
            child: directorioAsync.when(
              loading: () => const Center(
                child: CircularProgressIndicator(color: AppTheme.primary),
              ),
              error: (e, _) => Center(child: Text('Error: $e')),
              data: (data) {
                final List personas = data['data'] ?? [];

                if (personas.isEmpty) {
                  return const EmptyStateWidget(
                    icon:    Icons.people_outline,
                    titulo:  'Directorio vacío',
                    mensaje: 'Importa un archivo Excel para\npoblar el directorio de personas.',
                  );
                }

                return ListView.builder(
                  padding:     const EdgeInsets.all(12),
                  itemCount:   personas.length,
                  itemBuilder: (_, i) {
                    final p = personas[i] as Map<String, dynamic>;
                    return Card(
                      margin: const EdgeInsets.only(bottom: 8),
                      child: ListTile(
                        leading: CircleAvatar(
                          backgroundColor: AppTheme.primary.withOpacity(0.1),
                          child: Text(
                            (p['nombre'] as String? ?? 'P')[0].toUpperCase(),
                            style: const TextStyle(
                              color:      AppTheme.primary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                        title: Text(
                          p['nombre'] ?? '',
                          style: const TextStyle(
                            fontWeight: FontWeight.w600,
                            fontSize:   14,
                          ),
                        ),
                        subtitle: Text(
                          'CI: ${p['cedula']} · ${(p['tipo'] ?? '').toString().toUpperCase()}'
                          '${p['carrera'] != null ? ' · ${p['carrera']}' : ''}'
                          '${p['facultad'] != null ? ' · ${p['facultad']}' : ''}',
                          style: const TextStyle(fontSize: 12),
                        ),
                        trailing: IconButton(
                          icon: const Icon(Icons.delete_outline,
                              color: AppTheme.error, size: 20),
                          onPressed: () async {
                            await ApiClient().dio.delete(
                              '${ApiEndpoints.adminDirectorio}/${p['id']}',
                            );
                            ref.invalidate(directorioProvider);
                          },
                        ),
                      ),
                    );
                  },
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}