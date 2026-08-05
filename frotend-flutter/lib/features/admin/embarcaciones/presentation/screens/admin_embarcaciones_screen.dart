import 'dart:typed_data';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import '../../../../../core/api/api_client.dart';
import '../../../../../core/api/api_endpoints.dart';
import '../../../../../core/theme/app_theme.dart';
import '../../../../../core/widgets/admin_nav.dart';
import '../../../../../core/widgets/admin_page_scaffold.dart';

final adminEmbarcacionesProvider =
    FutureProvider.autoDispose<List<dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.embarcaciones);
  if (response.statusCode == 200) {
    return response.data['data'] ?? response.data;
  }
  throw Exception('Error al cargar embarcaciones');
});

class AdminEmbarcacionesScreen extends ConsumerWidget {
  const AdminEmbarcacionesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final embarcacionesAsync = ref.watch(adminEmbarcacionesProvider);

    return AdminPageScaffold(
      navItem: AdminNavItem.embarcaciones,
      actions: [
        IconButton(
          icon:      const Icon(Icons.refresh),
          onPressed: () => ref.invalidate(adminEmbarcacionesProvider),
        ),
      ],
      floatingActionButton: FloatingActionButton.extended(
        onPressed:       () => _formEmbarcacion(context, ref),
        icon:            const Icon(Icons.add),
        label:           const Text('Nueva'),
        backgroundColor: const Color.fromARGB(255, 253, 253, 253),
      ),
      body: embarcacionesAsync.when(
        loading: () => const Center(
          child: CircularProgressIndicator(color: AppTheme.primary),
        ),
        error: (e, _) => Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 48, color: AppTheme.error),
              const SizedBox(height: 12),
              Text('Error: $e'),
              const SizedBox(height: 12),
              ElevatedButton(
                onPressed: () => ref.invalidate(adminEmbarcacionesProvider),
                child: const Text('Reintentar'),
              ),
            ],
          ),
        ),
        data: (embarcaciones) => embarcaciones.isEmpty
            ? const Center(child: Text('No hay embarcaciones registradas'))
            : RefreshIndicator(
                onRefresh: () async =>
                    ref.invalidate(adminEmbarcacionesProvider),
                child: ListView.builder(
                  padding:     const EdgeInsets.all(16),
                  itemCount:   embarcaciones.length,
                  itemBuilder: (_, i) => _EmbarcacionCard(
                    embarcacion: embarcaciones[i] as Map<String, dynamic>,
                    onEdit: () => _formEmbarcacion(
                      context,
                      ref,
                      embarcacion: embarcaciones[i] as Map<String, dynamic>,
                    ),
                    onDelete: () => _eliminar(
                      context,
                      ref,
                      embarcaciones[i]['id'] as int,
                      embarcaciones[i]['nombre'] ?? '',
                    ),
                  ),
                ),
              ),
      ),
    );
  }

  Future<void> _eliminar(
    BuildContext context,
    WidgetRef ref,
    int id,
    String nombre,
  ) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title:   const Text('Eliminar embarcación'),
        content: Text('¿Estás seguro de eliminar "$nombre"?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext, false),
            child: const Text('Cancelar'),
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

    try {
      final response = await ApiClient().dio.delete(
        ApiEndpoints.adminEmbarcacion(id),
      );

      if (response.statusCode == 200) {
        ref.invalidate(adminEmbarcacionesProvider);
        if (context.mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content:         Text('Embarcación eliminada correctamente'),
              backgroundColor: AppTheme.success,
            ),
          );
        }
      } else {
        throw Exception(
          response.data['error'] ?? 'Error al eliminar',
        );
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content:         Text(e.toString().replaceAll('Exception: ', '')),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  Future<void> _formEmbarcacion(
    BuildContext context,
    WidgetRef ref, {
    Map<String, dynamic>? embarcacion,
  }) async {
    final formKey       = GlobalKey<FormState>();
    final nombreCtrl    = TextEditingController(text: embarcacion?['nombre'] ?? '');
    final capacidadCtrl = TextEditingController(
      text: embarcacion?['capacidad']?.toString() ?? '',
    );
    final descCtrl = TextEditingController(
      text: embarcacion?['descripcion'] ?? '',
    );
    String estado    = embarcacion?['estado'] ?? 'disponible';
    XFile?     imagen;
    Uint8List? imagenBytes;
    bool   isLoading = false;
    final  esEdicion = embarcacion != null;

    await showModalBottomSheet(
      context:            context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setState) => Padding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(ctx).viewInsets.bottom,
            left:   24,
            right:  24,
            top:    24,
          ),
          child: Form(
            key: formKey,
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize:       MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    esEdicion ? 'Editar embarcación' : 'Nueva embarcación',
                    style: const TextStyle(
                      fontSize:   18,
                      fontWeight: FontWeight.w700,
                      color:      AppTheme.primary,
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Nombre
                  TextFormField(
                    controller: nombreCtrl,
                    decoration: const InputDecoration(
                      labelText:  'Nombre',
                      prefixIcon: Icon(Icons.directions_boat_outlined),
                    ),
                    validator: (v) =>
                        v == null || v.isEmpty ? 'Requerido' : null,
                  ),
                  const SizedBox(height: 12),

                  // Capacidad
                  TextFormField(
                    controller:   capacidadCtrl,
                    keyboardType: TextInputType.number,
                    decoration: const InputDecoration(
                      labelText:  'Capacidad (personas)',
                      prefixIcon: Icon(Icons.people_outline),
                    ),
                    validator: (v) {
                      if (v == null || v.isEmpty) return 'Requerido';
                      if (int.tryParse(v) == null) return 'Número inválido';
                      return null;
                    },
                  ),
                  const SizedBox(height: 12),

                  // Descripción
                  TextFormField(
                    controller: descCtrl,
                    maxLines:   2,
                    decoration: const InputDecoration(
                      labelText:  'Descripción (opcional)',
                      prefixIcon: Icon(Icons.description_outlined),
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Estado
                  DropdownButtonFormField<String>(
                    value: estado,
                    decoration: const InputDecoration(
                      labelText:  'Estado',
                      prefixIcon: Icon(Icons.info_outline),
                    ),
                    items: const [
                      DropdownMenuItem(
                        value: 'disponible',
                        child: Text('Disponible'),
                      ),
                      DropdownMenuItem(
                        value: 'mantenimiento',
                        child: Text('Mantenimiento'),
                      ),
                    ],
                    onChanged: (v) => setState(() => estado = v!),
                  ),
                  const SizedBox(height: 12),

                  // Vista previa de la imagen actual / seleccionada
                  if (imagenBytes case final bytes?)
                    ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: Image.memory(
                        bytes,
                        height: 140,
                        width:  double.infinity,
                        fit:    BoxFit.cover,
                      ),
                    )
                  else if (embarcacion?['imagen_url'] != null)
                    ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: CachedNetworkImage(
                        imageUrl: embarcacion!['imagen_url'],
                        height:   140,
                        width:    double.infinity,
                        fit:      BoxFit.cover,
                        memCacheWidth: 800,
                        placeholder: (_, __) => Container(
                          height: 140,
                          color:  Colors.grey[100],
                          child: const Center(
                            child: CircularProgressIndicator(strokeWidth: 2),
                          ),
                        ),
                        errorWidget: (_, __, ___) => Container(
                          height: 140,
                          color:  Colors.grey[100],
                          child: const Center(
                            child: Icon(Icons.broken_image_outlined,
                                color: AppTheme.textMuted),
                          ),
                        ),
                      ),
                    ),
                  if (imagen != null || embarcacion?['imagen_url'] != null)
                    const SizedBox(height: 8),

                  // Imagen
                  InkWell(
                    onTap: () async {
                      final picked = await ImagePicker().pickImage(
                        source:     ImageSource.gallery,
                        maxWidth:   800,
                        imageQuality: 80,
                      );
                      if (picked != null) {
                        final bytes = await picked.readAsBytes();
                        setState(() {
                          imagen      = picked;
                          imagenBytes = bytes;
                        });
                      }
                    },
                    borderRadius: BorderRadius.circular(10),
                    child: Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color:        Colors.grey[100],
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(color: Colors.grey[300]!),
                      ),
                      child: Row(
                        children: [
                          const Icon(Icons.image_outlined,
                              color: AppTheme.primary),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Text(
                              imagen != null
                                  ? imagen!.name
                                  : (embarcacion?['imagen_url'] != null
                                      ? 'Cambiar imagen'
                                      : 'Seleccionar imagen (opcional)'),
                              style: TextStyle(
                                color: imagen != null
                                    ? AppTheme.primary
                                    : AppTheme.textMuted,
                              ),
                            ),
                          ),
                          if (imagen != null)
                            IconButton(
                              icon:      const Icon(Icons.close, size: 18),
                              onPressed: () => setState(() {
                                imagen      = null;
                                imagenBytes = null;
                              }),
                              padding:   EdgeInsets.zero,
                              constraints: const BoxConstraints(),
                            ),
                        ],
                      ),
                    ),
                  ),

                  const SizedBox(height: 20),

                  // Botón
                  ElevatedButton(
                    onPressed: isLoading
                        ? null
                        : () async {
                            if (!formKey.currentState!.validate()) return;
                            setState(() => isLoading = true);

                            try {
                              final formData = FormData.fromMap({
                                'nombre'     : nombreCtrl.text.trim(),
                                'capacidad'  : int.parse(capacidadCtrl.text),
                                'estado'     : estado,
                                'descripcion': descCtrl.text.trim(),
                                if (imagenBytes != null)
                                  'imagen': MultipartFile.fromBytes(
                                    imagenBytes!,
                                    filename: imagen!.name,
                                  ),
                                if (esEdicion) '_method': 'PUT',
                              });

                              final response = esEdicion
                                  ? await ApiClient().dio.post(
                                      ApiEndpoints.adminEmbarcacion(
                                          embarcacion['id']),
                                      data: formData,
                                    )
                                  : await ApiClient().dio.post(
                                      ApiEndpoints.adminEmbarcaciones,
                                      data: formData,
                                    );

                              if (response.statusCode == 200 ||
                                  response.statusCode == 201) {
                                ref.invalidate(adminEmbarcacionesProvider);
                                if (ctx.mounted) Navigator.pop(ctx);
                                if (context.mounted) {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(
                                      content: Text(
                                        esEdicion
                                            ? 'Embarcación actualizada'
                                            : 'Embarcación creada',
                                      ),
                                      backgroundColor: AppTheme.success,
                                    ),
                                  );
                                }
                              } else {
                                throw Exception(
                                  response.data['error'] ??
                                      response.data['message'] ??
                                      'Error',
                                );
                              }
                            } catch (e) {
                              setState(() => isLoading = false);
                              if (ctx.mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text(
                                      e.toString().replaceAll('Exception: ', ''),
                                    ),
                                    backgroundColor: AppTheme.error,
                                  ),
                                );
                              }
                            }
                          },
                    style: ElevatedButton.styleFrom(
                      minimumSize: const Size(double.infinity, 48),
                    ),
                    child: isLoading
                        ? const SizedBox(
                            height: 20,
                            width:  20,
                            child:  CircularProgressIndicator(
                              color:       Colors.white,
                              strokeWidth: 2,
                            ),
                          )
                        : Text(esEdicion
                            ? 'Actualizar embarcación'
                            : 'Crear embarcación'),
                  ),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _EmbarcacionCard extends StatelessWidget {
  final Map<String, dynamic> embarcacion;
  final VoidCallback         onEdit;
  final VoidCallback         onDelete;

  const _EmbarcacionCard({
    required this.embarcacion,
    required this.onEdit,
    required this.onDelete,
  });

  @override
  Widget build(BuildContext context) {
    final estado    = embarcacion['estado'] as String? ?? '';
    final capacidad = embarcacion['capacidad'] as int? ?? 0;
    final imagenUrl = embarcacion['imagen_url'] as String?;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Row(
          children: [

            // Imagen o icono
            Container(
              width:        56,
              height:       56,
              decoration:   BoxDecoration(
                color:        AppTheme.primary.withOpacity(0.1),
                borderRadius: BorderRadius.circular(10),
                image: imagenUrl != null
                    ? DecorationImage(
                        image: NetworkImage(imagenUrl),
                        fit:   BoxFit.cover,
                      )
                    : null,
              ),
              child: imagenUrl == null
                  ? const Icon(
                      Icons.directions_boat_rounded,
                      color: AppTheme.primary,
                      size:  28,
                    )
                  : null,
            ),
            const SizedBox(width: 12),

            // Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    embarcacion['nombre'] ?? '',
                    style: const TextStyle(
                      fontSize:   15,
                      fontWeight: FontWeight.w700,
                      color:      AppTheme.primary,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    'Capacidad: $capacidad pasajeros',
                    style: const TextStyle(
                      fontSize: 12,
                      color:    AppTheme.textMuted,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical:   2,
                    ),
                    decoration: BoxDecoration(
                      color: (estado == 'disponible'
                              ? AppTheme.success
                              : AppTheme.warning)
                          .withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: estado == 'disponible'
                            ? AppTheme.success
                            : AppTheme.warning,
                      ),
                    ),
                    child: Text(
                      estado.toUpperCase(),
                      style: TextStyle(
                        fontSize:   10,
                        fontWeight: FontWeight.w700,
                        color: estado == 'disponible'
                            ? AppTheme.success
                            : AppTheme.warning,
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Acciones
            Column(
              children: [
                IconButton(
                  icon:      const Icon(Icons.edit_outlined,
                      color: AppTheme.primary),
                  onPressed: onEdit,
                  tooltip:   'Editar',
                  padding:   EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
                const SizedBox(height: 8),
                IconButton(
                  icon:      const Icon(Icons.delete_outline,
                      color: AppTheme.error),
                  onPressed: onDelete,
                  tooltip:   'Eliminar',
                  padding:   EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}