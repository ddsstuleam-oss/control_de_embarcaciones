import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/api/api_client.dart';
import '../../../../../core/api/api_endpoints.dart';
import '../../../../../core/theme/app_theme.dart';
import '../../../../../core/widgets/admin_nav.dart';
import '../../../../../core/widgets/admin_page_scaffold.dart';
import '../../../../../core/widgets/empty_state_widget.dart';
import '../../../../../core/widgets/password_strength_meter.dart';

final usuariosProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.adminUsuarios);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar usuarios');
});

class UsuariosScreen extends ConsumerStatefulWidget {
  const UsuariosScreen({super.key});

  @override
  ConsumerState<UsuariosScreen> createState() => _UsuariosScreenState();
}

class _UsuariosScreenState extends ConsumerState<UsuariosScreen> {
  String _buscar = '';
  String _rolFiltro = 'todos';

  @override
  Widget build(BuildContext context) {
    final usuariosAsync = ref.watch(usuariosProvider);

    return AdminPageScaffold(
      navItem: AdminNavItem.usuarios,
      actions: [
        IconButton(
          icon:      const Icon(Icons.refresh),
          onPressed: () => ref.invalidate(usuariosProvider),
        ),
        IconButton(
          icon:      const Icon(Icons.person_add_outlined),
          onPressed: () => _crearUsuario(context),
          tooltip:   'Nuevo usuario',
        ),
      ],
      body: Column(
        children: [

          // Buscador y filtro
          Container(
            color:   Colors.white,
            padding: const EdgeInsets.all(12),
            child: Column(
              children: [
                TextField(
                  onChanged: (v) => setState(() => _buscar = v),
                  decoration: InputDecoration(
                    hintText:    'Buscar por nombre, cédula o email...',
                    prefixIcon:  const Icon(Icons.search),
                    suffixIcon:  _buscar.isNotEmpty
                        ? IconButton(
                            icon:      const Icon(Icons.close),
                            onPressed: () => setState(() => _buscar = ''),
                          )
                        : null,
                    isDense:     true,
                    filled:      true,
                    fillColor:   Colors.grey[100],
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide:   BorderSide.none,
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: ['todos', 'admin', 'operador', 'usuario']
                        .map((rol) => Padding(
                              padding: const EdgeInsets.only(right: 8),
                              child: FilterChip(
                                label:     Text(rol.toUpperCase()),
                                selected:  _rolFiltro == rol,
                                onSelected: (_) =>
                                    setState(() => _rolFiltro = rol),
                                selectedColor:
                                    AppTheme.primary.withOpacity(0.15),
                                checkmarkColor: AppTheme.primary,
                                labelStyle: TextStyle(
                                  color: _rolFiltro == rol
                                      ? AppTheme.primary
                                      : AppTheme.textMuted,
                                  fontWeight: FontWeight.w600,
                                  fontSize:   11,
                                ),
                              ),
                            ))
                        .toList(),
                  ),
                ),
              ],
            ),
          ),

          // Lista
          Expanded(
            child: usuariosAsync.when(
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
                      onPressed: () => ref.invalidate(usuariosProvider),
                      child: const Text('Reintentar'),
                    ),
                  ],
                ),
              ),
              data: (data) {
                final List usuarios = data['data'] ?? [];

                // Filtrar
                final filtrados = usuarios.where((u) {
                  final user = u as Map<String, dynamic>;
                  final matchBuscar = _buscar.isEmpty ||
                      (user['nombre'] as String? ?? '')
                          .toLowerCase()
                          .contains(_buscar.toLowerCase()) ||
                      (user['cedula'] as String? ?? '')
                          .contains(_buscar) ||
                      (user['email'] as String? ?? '')
                          .toLowerCase()
                          .contains(_buscar.toLowerCase());

                  final matchRol = _rolFiltro == 'todos' ||
                      user['rol'] == _rolFiltro;

                  return matchBuscar && matchRol;
                }).toList();

                if (filtrados.isEmpty) {
                  return const EmptyStateWidget(
                    icon:    Icons.people_outline,
                    titulo:  'Sin resultados',
                    mensaje: 'No se encontraron usuarios\ncon los filtros aplicados.',
                  );
                }

                return RefreshIndicator(
                  onRefresh: () async =>
                      ref.invalidate(usuariosProvider),
                  child: ListView.builder(
                    padding:     const EdgeInsets.all(12),
                    itemCount:   filtrados.length,
                    itemBuilder: (_, i) => _UsuarioCard(
                      usuario:  filtrados[i] as Map<String, dynamic>,
                      onToggle: () => _toggleUsuario(
                        context,
                        filtrados[i]['id'],
                        filtrados[i]['activo'] as bool? ?? true,
                        filtrados[i]['nombre'] ?? '',
                      ),
                      onCambiarRol: () => _cambiarRol(
                        context,
                        filtrados[i]['id'],
                        filtrados[i]['rol'] ?? 'usuario',
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Future<void> _toggleUsuario(
    BuildContext context,
    int id,
    bool activo,
    String nombre,
  ) async {
    final accion  = activo ? 'desactivar' : 'activar';
    final confirm = await showDialog<bool>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title:   Text('${accion.capitalize()} usuario'),
        content: Text('¿Estás seguro de $accion a $nombre?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext, false),
            child: const Text('Cancelar'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(dialogContext, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: activo ? AppTheme.error : AppTheme.success,
            ),
            child: Text(accion.capitalize()),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    try {
      final response = await ApiClient().dio.patch(
        ApiEndpoints.adminToggleUsuario(id),
      );
      if (response.statusCode != 200) {
        throw Exception(
          response.data['error'] ??
              response.data['message'] ??
              'Error al $accion usuario',
        );
      }
      ref.invalidate(usuariosProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              'Usuario ${activo ? 'desactivado' : 'activado'} correctamente',
            ),
            backgroundColor: AppTheme.success,
          ),
        );
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content:         Text('Error: $e'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  Future<void> _cambiarRol(
    BuildContext context,
    int id,
    String rolActual,
  ) async {
    String rolSeleccionado = rolActual;

    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => StatefulBuilder(
        builder: (ctx, setState) => AlertDialog(
          title: const Text('Cambiar rol'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: ['admin', 'operador', 'usuario'].map((rol) {
              return RadioListTile<String>(
                title:    Text(rol.capitalize()),
                value:    rol,
                groupValue: rolSeleccionado,
                activeColor: AppTheme.primary,
                onChanged: (v) => setState(() => rolSeleccionado = v!),
              );
            }).toList(),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx, false),
              child: const Text('Cancelar'),
            ),
            ElevatedButton(
              onPressed: () => Navigator.pop(ctx, true),
              child: const Text('Guardar'),
            ),
          ],
        ),
      ),
    );

    if (confirm != true || rolSeleccionado == rolActual) return;

    try {
      final response = await ApiClient().dio.patch(
        ApiEndpoints.adminRolUsuario(id),
        data: {'rol': rolSeleccionado},
      );
      if (response.statusCode != 200) {
        throw Exception(
          response.data['error'] ??
              response.data['message'] ??
              'Error al actualizar rol',
        );
      }
      ref.invalidate(usuariosProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content:         Text('Rol actualizado correctamente'),
            backgroundColor: AppTheme.success,
          ),
        );
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content:         Text('Error: $e'),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    }
  }

  Future<void> _crearUsuario(BuildContext context) async {
    final formKey    = GlobalKey<FormState>();
    final cedulaCtrl = TextEditingController();
    final nameCtrl   = TextEditingController();
    final emailCtrl  = TextEditingController();
    final passCtrl   = TextEditingController();
    String rol       = 'usuario';
    bool isLoading   = false;

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
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Nuevo usuario',
                    style: TextStyle(
                      fontSize:   18,
                      fontWeight: FontWeight.w700,
                      color:      AppTheme.primary,
                    ),
                  ),
                  const SizedBox(height: 16),

                  TextFormField(
                    controller:   cedulaCtrl,
                    keyboardType: TextInputType.number,
                    maxLength:    10,
                    decoration: const InputDecoration(
                      labelText:   'Cédula',
                      prefixIcon:  Icon(Icons.badge_outlined),
                      counterText: '',
                    ),
                    validator: (v) {
                      if (v == null || v.isEmpty) return 'Requerido';
                      if (v.length != 10) return '10 dígitos';
                      return null;
                    },
                  ),
                  const SizedBox(height: 12),

                  TextFormField(
                    controller: nameCtrl,
                    decoration: const InputDecoration(
                      labelText:  'Nombre completo',
                      prefixIcon: Icon(Icons.person_outline),
                    ),
                    validator: (v) =>
                        v == null || v.isEmpty ? 'Requerido' : null,
                  ),
                  const SizedBox(height: 12),

                  TextFormField(
                    controller:   emailCtrl,
                    keyboardType: TextInputType.emailAddress,
                    decoration: const InputDecoration(
                      labelText:  'Email',
                      prefixIcon: Icon(Icons.email_outlined),
                    ),
                    validator: (v) {
                      if (v == null || v.isEmpty) return 'Requerido';
                      if (!v.contains('@')) return 'Email inválido';
                      return null;
                    },
                  ),
                  const SizedBox(height: 12),

                  TextFormField(
                    controller:  passCtrl,
                    obscureText: true,
                    onChanged: (_) => setState(() {}),
                    decoration: const InputDecoration(
                      labelText:  'Contraseña',
                      prefixIcon: Icon(Icons.lock_outline),
                    ),
                    validator: (v) {
                      if (v == null || v.isEmpty) return 'Requerido';
                      if (v.length < 8) return 'Mínimo 8 caracteres';
                      if (!RegExp(r'[a-z]').hasMatch(v) ||
                          !RegExp(r'[A-Z]').hasMatch(v)) {
                        return 'Combina mayúsculas y minúsculas';
                      }
                      if (!RegExp(r'[0-9]').hasMatch(v)) {
                        return 'Incluye al menos un número';
                      }
                      return null;
                    },
                  ),
                  PasswordStrengthMeter(password: passCtrl.text),
                  const SizedBox(height: 12),

                  DropdownButtonFormField<String>(
                    value:       rol,
                    decoration: const InputDecoration(
                      labelText:  'Rol',
                      prefixIcon: Icon(Icons.admin_panel_settings_outlined),
                    ),
                    items: const [
                      DropdownMenuItem(value: 'usuario',   child: Text('Usuario')),
                      DropdownMenuItem(value: 'operador',  child: Text('Operador')),
                      DropdownMenuItem(value: 'admin',     child: Text('Admin')),
                    ],
                    onChanged: (v) => setState(() => rol = v!),
                  ),

                  const SizedBox(height: 20),

                  ElevatedButton(
                    onPressed: isLoading
                        ? null
                        : () async {
                            if (!formKey.currentState!.validate()) return;
                            setState(() => isLoading = true);

                            try {
                              final response = await ApiClient().dio.post(
                                ApiEndpoints.adminUsuarios,
                                data: {
                                  'cedula'   : cedulaCtrl.text.trim(),
                                  'name'     : nameCtrl.text.trim(),
                                  'email'    : emailCtrl.text.trim(),
                                  'password' : passCtrl.text,
                                  'rol'      : rol,
                                },
                              );
                              if (response.statusCode != 201) {
                                throw Exception(
                                  response.data['error'] ??
                                      response.data['message'] ??
                                      'Error al crear usuario',
                                );
                              }
                              ref.invalidate(usuariosProvider);
                              if (ctx.mounted) Navigator.pop(ctx);
                              if (context.mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(
                                    content: Text('Usuario creado correctamente'),
                                    backgroundColor: AppTheme.success,
                                  ),
                                );
                              }
                            } catch (e) {
                              setState(() => isLoading = false);
                              if (ctx.mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('Error: $e'),
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
                        : const Text('Crear usuario'),
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

class _UsuarioCard extends StatelessWidget {
  final Map<String, dynamic> usuario;
  final VoidCallback         onToggle;
  final VoidCallback         onCambiarRol;

  const _UsuarioCard({
    required this.usuario,
    required this.onToggle,
    required this.onCambiarRol,
  });

  Color get _rolColor {
    switch (usuario['rol']) {
      case 'admin':    return AppTheme.error;
      case 'operador': return AppTheme.warning;
      default:         return AppTheme.info;
    }
  }

  @override
  Widget build(BuildContext context) {
    final activo = usuario['activo'] as bool? ?? true;

    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          children: [

            // Avatar
            CircleAvatar(
              radius:          22,
              backgroundColor: activo
                  ? AppTheme.primary.withOpacity(0.1)
                  : Colors.grey[200],
              child: Text(
                (usuario['nombre'] as String? ?? 'U')[0].toUpperCase(),
                style: TextStyle(
                  fontSize:   18,
                  fontWeight: FontWeight.w700,
                  color: activo ? AppTheme.primary : Colors.grey,
                ),
              ),
            ),
            const SizedBox(width: 12),

            // Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          usuario['nombre'] ?? '',
                          style: TextStyle(
                            fontSize:   14,
                            fontWeight: FontWeight.w600,
                            color: activo
                                ? AppTheme.primary
                                : AppTheme.textMuted,
                          ),
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical:   2,
                        ),
                        decoration: BoxDecoration(
                          color:        _rolColor.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: _rolColor),
                        ),
                        child: Text(
                          (usuario['rol'] as String? ?? '').toUpperCase(),
                          style: TextStyle(
                            fontSize:   9,
                            fontWeight: FontWeight.w700,
                            color:      _rolColor,
                          ),
                        ),
                      ),
                    ],
                  ),
                  Text(
                    usuario['cedula'] ?? '',
                    style: const TextStyle(
                      fontSize:   12,
                      color:      AppTheme.textMuted,
                      fontFamily: 'monospace',
                    ),
                  ),
                  Text(
                    usuario['email'] ?? '',
                    style: const TextStyle(
                      fontSize: 11,
                      color:    AppTheme.textMuted,
                    ),
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),

            // Acciones
            Column(
              children: [
                IconButton(
                  icon: Icon(
                    activo
                        ? Icons.toggle_on_rounded
                        : Icons.toggle_off_rounded,
                    color: activo ? AppTheme.success : Colors.grey,
                    size:  32,
                  ),
                  onPressed: onToggle,
                  tooltip:   activo ? 'Desactivar' : 'Activar',
                  padding:   EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
                const SizedBox(height: 4),
                IconButton(
                  icon: const Icon(
                    Icons.manage_accounts_outlined,
                    color: AppTheme.primary,
                    size:  22,
                  ),
                  onPressed:   onCambiarRol,
                  tooltip:     'Cambiar rol',
                  padding:     EdgeInsets.zero,
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

extension StringExt on String {
  String capitalize() =>
      isEmpty ? this : '${this[0].toUpperCase()}${substring(1)}';
}