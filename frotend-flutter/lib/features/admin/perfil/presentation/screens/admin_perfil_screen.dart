import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/api/api_client.dart';
import '../../../../../core/api/api_endpoints.dart';
import '../../../../../core/theme/app_theme.dart';
import '../../../../../core/widgets/admin_nav.dart';
import '../../../../../core/widgets/admin_page_scaffold.dart';
import '../../../../../core/widgets/user_avatar.dart';
import '../../../../../core/widgets/profile_photo_picker.dart';
import '../../../../../features/auth/providers/auth_provider.dart';
import '../../../../perfil/providers/perfil_provider.dart';

final adminPerfilProvider =
    FutureProvider.autoDispose<Map<String, dynamic>>((ref) async {
  final response = await ApiClient().dio.get(ApiEndpoints.perfil);
  if (response.statusCode == 200) return response.data;
  throw Exception('Error al cargar perfil');
});

class AdminPerfilScreen extends ConsumerWidget {
  const AdminPerfilScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final perfilAsync = ref.watch(adminPerfilProvider);
    final subiendoFoto = ref.watch(subiendoFotoPerfilProvider);

    return AdminPageScaffold(
      navItem: AdminNavItem.perfil,
      // En web el botón de "Cerrar sesión" del sidebar (siempre visible,
      // vive fuera del contenido de cada página) ya cubre esto — no
      // duplicamos uno acá dentro, que además choca con la redirección
      // al cerrar sesión por vivir dentro del Navigator anidado del
      // ShellRoute.
      actions: kIsWeb
          ? null
          : [
              IconButton(
                icon:    const Icon(Icons.logout),
                tooltip: 'Cerrar sesión',
                onPressed: () => confirmarCerrarSesion(context, ref),
              ),
            ],
      body: perfilAsync.when(
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
                onPressed: () => ref.invalidate(adminPerfilProvider),
                child: const Text('Reintentar'),
              ),
            ],
          ),
        ),
        data: (perfil) {
          final usuario      = perfil['usuario']      as Map<String, dynamic>? ?? {};
          final estadisticas = perfil['estadisticas'] as Map<String, dynamic>? ?? {};

          return SingleChildScrollView(
            child: Column(
              children: [

                // ── Header ───────────────────────────────────────
                Container(
                  width:   double.infinity,
                  padding: const EdgeInsets.fromLTRB(24, 32, 24, 40),
                  decoration: const BoxDecoration(
                    gradient: LinearGradient(
                      begin:  Alignment.topLeft,
                      end:    Alignment.bottomRight,
                      colors: [
                        AppTheme.primary,
                        Color(0xFF16213E),
                      ],
                    ),
                  ),
                  child: Column(
                    children: [

                      // Avatar con badge de admin
                      UserAvatar(
                        nombre:          usuario['nombre'] as String? ?? 'A',
                        fotoUrl:         usuario['foto_url'] as String?,
                        radius:          48,
                        backgroundColor: AppTheme.accent,
                        editable:        true,
                        uploading:       subiendoFoto,
                        onTap:           () => _cambiarFoto(context, ref),
                        roleBadge: Container(
                          padding: const EdgeInsets.all(4),
                          decoration: BoxDecoration(
                            color:  Colors.amber,
                            shape:  BoxShape.circle,
                            border: Border.all(
                              color: AppTheme.primary,
                              width: 2,
                            ),
                          ),
                          child: const Icon(
                            Icons.admin_panel_settings,
                            color: Colors.white,
                            size:  14,
                          ),
                        ),
                      ),

                      const SizedBox(height: 16),

                      Text(
                        usuario['nombre'] ?? '',
                        style: const TextStyle(
                          color:      Colors.white,
                          fontSize:   22,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical:    5,
                        ),
                        decoration: BoxDecoration(
                          color:        Colors.amber.withOpacity(0.2),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: Colors.amber.withOpacity(0.6),
                          ),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.admin_panel_settings,
                                color: Colors.amber, size: 14),
                            SizedBox(width: 6),
                            Text(
                              'ADMINISTRADOR',
                              style: TextStyle(
                                color:         Colors.amber,
                                fontSize:      11,
                                fontWeight:    FontWeight.w700,
                                letterSpacing: 1,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        usuario['email'] ?? '',
                        style: const TextStyle(
                          color:    Colors.white60,
                          fontSize: 13,
                        ),
                      ),
                    ],
                  ),
                ),

                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: [

                      // ── Estadísticas ────────────────────────────
                      Row(
                        children: [
                          _StatCard(
                            label: 'Reservas',
                            value: '${_n(estadisticas['total_reservas'])}',
                            icon:  Icons.book_online,
                            color: AppTheme.primary,
                          ),
                          const SizedBox(width: 12),
                          _StatCard(
                            label: 'Activas',
                            value: '${_n(estadisticas['reservas_activas'])}',
                            icon:  Icons.check_circle_outline,
                            color: AppTheme.success,
                          ),
                          const SizedBox(width: 12),
                          _StatCard(
                            label: 'Canceladas',
                            value: '${_n(estadisticas['reservas_canceladas'])}',
                            icon:  Icons.cancel_outlined,
                            color: AppTheme.error,
                          ),
                        ],
                      ),

                      const SizedBox(height: 16),

                      // ── Info personal ───────────────────────────
                      _InfoCard(
                        title: 'Información personal',
                        children: [
                          _InfoRow(
                            icon:  Icons.badge_outlined,
                            label: 'Cédula',
                            value: usuario['cedula'] ?? '',
                          ),
                          _InfoRow(
                            icon:  Icons.email_outlined,
                            label: 'Email',
                            value: usuario['email'] ?? '',
                          ),
                          _InfoRow(
                            icon:  Icons.calendar_today_outlined,
                            label: 'Miembro desde',
                            value: usuario['miembro_desde'] ?? '',
                          ),
                        ],
                      ),

                      const SizedBox(height: 16),

                      // ── Seguridad ───────────────────────────────
                      _InfoCard(
                        title: 'Seguridad',
                        children: [
                          _InfoRow(
                            icon:  Icons.lock_clock_outlined,
                            label: 'Días para vencer contraseña',
                            value: '${_n(usuario['dias_para_vencer'])} días',
                            valueColor: _n(usuario['dias_para_vencer']) < 10
                                ? AppTheme.error
                                : AppTheme.success,
                          ),
                        ],
                      ),

                      const SizedBox(height: 16),

                      // ── Acciones ────────────────────────────────
                      _InfoCard(
                        title: 'Opciones',
                        children: [
                          ListTile(
                            contentPadding: EdgeInsets.zero,
                            leading: const Icon(
                              Icons.lock_outline,
                              color: AppTheme.primary,
                            ),
                            title:   const Text('Cambiar contraseña'),
                            trailing: const Icon(Icons.chevron_right),
                            onTap:   () => _cambiarPassword(context, ref),
                          ),
                          ListTile(
                            contentPadding: EdgeInsets.zero,
                            leading: const Icon(
                              Icons.history_outlined,
                              color: AppTheme.primary,
                            ),
                            title: const Text('Actividad reciente'),
                            trailing: const Icon(Icons.chevron_right),
                            onTap: () => context.push('/perfil/actividad'),
                          ),

                        ],
                      ),

                      // En web ya está el de la barra lateral — ver nota
                      // junto a `actions` más arriba.
                      if (!kIsWeb) ...[
                        const SizedBox(height: 16),

                        // ── Cerrar sesión ───────────────────────────
                        OutlinedButton.icon(
                          onPressed: () => confirmarCerrarSesion(context, ref),
                          icon:  const Icon(Icons.logout, color: AppTheme.error),
                          label: const Text(
                            'Cerrar sesión',
                            style: TextStyle(color: AppTheme.error),
                          ),
                          style: OutlinedButton.styleFrom(
                            minimumSize: const Size(double.infinity, 48),
                            side: const BorderSide(color: AppTheme.error),
                          ),
                        ),
                      ],

                      const SizedBox(height: 32),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  int _n(dynamic v) => (v as num?)?.toInt() ?? 0;

  Future<void> _cambiarFoto(BuildContext context, WidgetRef ref) async {
    try {
      final foto = await elegirYRecortarFotoPerfil(context);
      if (foto == null) return;

      ref.read(subiendoFotoPerfilProvider.notifier).state = true;
      final data = await ref.read(perfilRepositoryProvider).subirFotoPerfil(foto);
      final usuario = data['usuario'] as Map<String, dynamic>?;
      if (usuario != null) {
        await ref.read(authProvider.notifier).actualizarUsuario(usuario);
      }
      ref.invalidate(adminPerfilProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Foto de perfil actualizada'),
            backgroundColor: AppTheme.success,
          ),
        );
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
    } finally {
      ref.read(subiendoFotoPerfilProvider.notifier).state = false;
    }
  }

  Future<void> _cambiarPassword(BuildContext context, WidgetRef ref) async {
    final formKey     = GlobalKey<FormState>();
    final currentCtrl = TextEditingController();
    final newCtrl     = TextEditingController();
    final confirmCtrl = TextEditingController();
    bool isLoading    = false;

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
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Cambiar contraseña',
                  style: TextStyle(
                    fontSize:   18,
                    fontWeight: FontWeight.w700,
                    color:      AppTheme.primary,
                  ),
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller:  currentCtrl,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText:  'Contraseña actual',
                    prefixIcon: Icon(Icons.lock_outline),
                  ),
                  validator: (v) =>
                      v == null || v.isEmpty ? 'Requerido' : null,
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller:  newCtrl,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText:  'Nueva contraseña',
                    prefixIcon: Icon(Icons.lock_outline),
                  ),
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Requerido';
                    if (v.length < 8) return 'Mínimo 8 caracteres';
                    return null;
                  },
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller:  confirmCtrl,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText:  'Confirmar contraseña',
                    prefixIcon: Icon(Icons.lock_outline),
                  ),
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Requerido';
                    if (v != newCtrl.text) return 'No coinciden';
                    return null;
                  },
                ),
                const SizedBox(height: 20),
                ElevatedButton(
                  onPressed: isLoading
                      ? null
                      : () async {
                          if (!formKey.currentState!.validate()) return;
                          setState(() => isLoading = true);
                          try {
                            await ApiClient().dio.post(
                              ApiEndpoints.updatePassword,
                              data: {
                                'current_password'         : currentCtrl.text,
                                'new_password'             : newCtrl.text,
                                'new_password_confirmation': confirmCtrl.text,
                              },
                            );
                            if (ctx.mounted) Navigator.pop(ctx);
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text(
                                      'Contraseña actualizada correctamente'),
                                  backgroundColor: AppTheme.success,
                                ),
                              );
                              await ref
                                  .read(authProvider.notifier)
                                  .logout();
                            }
                          } catch (e) {
                            setState(() => isLoading = false);
                            if (ctx.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                  content: Text(e
                                      .toString()
                                      .replaceAll('Exception: ', '')),
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
                      : const Text('Actualizar contraseña'),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ── Widgets ───────────────────────────────────────────────────────────────────

class _StatCard extends StatelessWidget {
  final String   label;
  final String   value;
  final IconData icon;
  final Color    color;

  const _StatCard({
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color:        Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color:     Colors.black.withOpacity(0.05),
              blurRadius: 8,
              offset:    const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 22),
            const SizedBox(height: 6),
            Text(
              value,
              style: TextStyle(
                fontSize:   20,
                fontWeight: FontWeight.w700,
                color:      color,
              ),
            ),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10,
                color:    AppTheme.textMuted,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}

class _InfoCard extends StatelessWidget {
  final String       title;
  final List<Widget> children;

  const _InfoCard({required this.title, required this.children});

  @override
  Widget build(BuildContext context) {
    return Container(
      width:   double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color:        Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color:     Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset:    const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize:   15,
              fontWeight: FontWeight.w700,
              color:      AppTheme.primary,
            ),
          ),
          const SizedBox(height: 12),
          ...children,
        ],
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final IconData icon;
  final String   label;
  final String   value;
  final Color?   valueColor;

  const _InfoRow({
    required this.icon,
    required this.label,
    required this.value,
    this.valueColor,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        children: [
          Icon(icon, size: 18, color: AppTheme.textMuted),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: const TextStyle(
                    fontSize: 11,
                    color:    AppTheme.textMuted,
                  ),
                ),
                Text(
                  value,
                  style: TextStyle(
                    fontSize:   14,
                    fontWeight: FontWeight.w600,
                    color:      valueColor ?? AppTheme.primary,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}