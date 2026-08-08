import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../providers/perfil_provider.dart';
import '../../../../features/auth/providers/auth_provider.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/user_avatar.dart';
import '../../../../core/widgets/profile_photo_picker.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../../core/widgets/password_strength_meter.dart';

class PerfilScreen extends ConsumerWidget {
  const PerfilScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final perfilAsync = ref.watch(perfilProvider);
    final subiendoFoto = ref.watch(subiendoFotoPerfilProvider);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Mi perfil'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            tooltip: 'Cerrar sesión',
            onPressed: () => _confirmarLogout(context, ref),
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 480,
        child: perfilAsync.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (e, _) => Center(child: Text('Error: $e')),
          data: (perfil) {
            final usuario = perfil['usuario'] as Map<String, dynamic>? ?? {};
            final estadisticas =
                perfil['estadisticas'] as Map<String, dynamic>? ?? {};

            return SingleChildScrollView(
              child: Column(
                children: [
                  // Header con avatar
                  Container(
                    width: double.infinity,
                    color: AppTheme.primary,
                    padding: const EdgeInsets.fromLTRB(24, 24, 24, 32),
                    child: Column(
                      children: [
                        UserAvatar(
                          nombre: usuario['nombre'] as String? ?? 'U',
                          fotoUrl: usuario['foto_url'] as String?,
                          radius: 40,
                          backgroundColor: AppTheme.accent,
                          editable: true,
                          uploading: subiendoFoto,
                          onTap: () => _cambiarFoto(context, ref),
                        ),
                        const SizedBox(height: 12),
                        Text(
                          usuario['nombre'] ?? '',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 20,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Text(
                            (usuario['rol'] as String? ?? '').toUpperCase(),
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        // Estadísticas
                        Row(
                          children: [
                            _StatCard(
                              label: 'Total reservas',
                              value:
                                  '${(estadisticas['total_reservas'] as num?)?.toInt() ?? 0}',
                              icon: Icons.book_online,
                              color: AppTheme.primary,
                            ),
                            const SizedBox(width: 12),
                            _StatCard(
                              label: 'Activas',
                              value:
                                  '${(estadisticas['reservas_activas'] as num?)?.toInt() ?? 0}',
                              icon: Icons.check_circle_outline,
                              color: AppTheme.success,
                            ),
                            const SizedBox(width: 12),
                            _StatCard(
                              label: 'Canceladas',
                              value:
                                  '${(estadisticas['reservas_canceladas'] as num?)?.toInt() ?? 0}',
                              icon: Icons.cancel_outlined,
                              color: AppTheme.error,
                            ),
                          ],
                        ),

                        const SizedBox(height: 16),

                        // Info personal
                        _InfoCard(
                          title: 'Información personal',
                          children: [
                            _InfoRow(
                              icon: Icons.badge_outlined,
                              label: 'Cédula',
                              value: usuario['cedula'] ?? '',
                            ),
                            _InfoRow(
                              icon: Icons.email_outlined,
                              label: 'Email',
                              value: usuario['email'] ?? '',
                              subtitulo:
                                  (usuario['email_pendiente'] as String?)
                                              ?.isNotEmpty ==
                                          true
                                      ? 'Confirmando cambio a: ${usuario['email_pendiente']}'
                                      : null,
                              onEdit: () => _cambiarEmail(
                                context,
                                ref,
                                usuario['email_pendiente'] as String?,
                              ),
                            ),
                            _InfoRow(
                              icon: Icons.phone_outlined,
                              label: 'Teléfono',
                              value: (usuario['telefono'] as String?)
                                      ?.isNotEmpty ==
                                      true
                                  ? usuario['telefono']
                                  : 'No registrado',
                              valueColor:
                                  (usuario['telefono'] as String?)
                                              ?.isNotEmpty ==
                                          true
                                      ? null
                                      : AppTheme.error,
                              onEdit: () => _editarTelefono(
                                context,
                                ref,
                                usuario['telefono'] as String?,
                              ),
                            ),
                            _InfoRow(
                              icon: Icons.calendar_today_outlined,
                              label: 'Miembro desde',
                              value: usuario['miembro_desde'] ?? '',
                            ),
                          ],
                        ),

                        const SizedBox(height: 16),

                        // Seguridad
                        _InfoCard(
                          title: 'Seguridad',
                          children: [
                            _InfoRow(
                              icon: Icons.lock_clock_outlined,
                              label: 'Días para vencer contraseña',
                              value:
                                  '${(usuario['dias_para_vencer'] as num?)?.toInt() ?? 0} días',
                              valueColor: ((usuario['dias_para_vencer'] as num?)
                                              ?.toInt() ??
                                          90) <
                                      10
                                  ? AppTheme.error
                                  : AppTheme.success,
                            ),
                          ],
                        ),

                        const SizedBox(height: 16),

                        // Acciones
                        _InfoCard(
                          title: 'Opciones',
                          children: [
                            ListTile(
                              contentPadding: EdgeInsets.zero,
                              leading: const Icon(
                                Icons.lock_outline,
                                color: AppTheme.primary,
                              ),
                              title: const Text('Cambiar contraseña'),
                              trailing: const Icon(Icons.chevron_right),
                              onTap: () => _cambiarPassword(context, ref),
                            ),
                            ListTile(
                              contentPadding: EdgeInsets.zero,
                              leading: const Icon(
                                Icons.book_online_outlined,
                                color: AppTheme.primary,
                              ),
                              title: const Text('Mis reservas'),
                              trailing: const Icon(Icons.chevron_right),
                              onTap: () => context.push('/reservas'),
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

                        const SizedBox(height: 16),

                        // Cerrar sesión
                        OutlinedButton.icon(
                          onPressed: () => _confirmarLogout(context, ref),
                          icon: const Icon(
                            Icons.logout,
                            color: AppTheme.error,
                          ),
                          label: const Text(
                            'Cerrar sesión',
                            style: TextStyle(color: AppTheme.error),
                          ),
                          style: OutlinedButton.styleFrom(
                            minimumSize: const Size(double.infinity, 48),
                            side: const BorderSide(color: AppTheme.error),
                          ),
                        ),

                        const SizedBox(height: 32),
                      ],
                    ),
                  ),
                ],
              ),
            );
          },
        ),
      ),
    );
  }

  Future<void> _cambiarFoto(BuildContext context, WidgetRef ref) async {
    try {
      final foto = await elegirYRecortarFotoPerfil(context);
      if (foto == null) return;

      ref.read(subiendoFotoPerfilProvider.notifier).state = true;
      final data =
          await ref.read(perfilRepositoryProvider).subirFotoPerfil(foto);
      final usuario = data['usuario'] as Map<String, dynamic>?;
      if (usuario != null) {
        await ref.read(authProvider.notifier).actualizarUsuario(usuario);
      }
      ref.invalidate(perfilProvider);
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

  Future<void> _editarTelefono(
    BuildContext context,
    WidgetRef ref,
    String? actual,
  ) async {
    final formKey = GlobalKey<FormState>();
    final telefonoCtrl = TextEditingController(text: actual ?? '');
    bool isLoading = false;

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setState) => Padding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(ctx).viewInsets.bottom,
            left: 24,
            right: 24,
            top: 24,
          ),
          child: Form(
            key: formKey,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Editar teléfono',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.primary,
                  ),
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: telefonoCtrl,
                  keyboardType: TextInputType.phone,
                  maxLength: 10,
                  decoration: const InputDecoration(
                    labelText: 'Teléfono',
                    prefixIcon: Icon(Icons.phone_outlined),
                    counterText: '',
                  ),
                  validator: (v) {
                    if (v == null || v.isEmpty) return 'Ingresa tu teléfono';
                    if (!RegExp(r'^\d{7,10}$').hasMatch(v)) {
                      return 'Debe tener entre 7 y 10 dígitos';
                    }
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
                            final data = await ref
                                .read(perfilRepositoryProvider)
                                .actualizarPerfil(
                                  telefono: telefonoCtrl.text.trim(),
                                );
                            final usuarioActualizado =
                                data['usuario'] as Map<String, dynamic>?;
                            if (usuarioActualizado != null) {
                              await ref
                                  .read(authProvider.notifier)
                                  .actualizarUsuario(usuarioActualizado);
                            }
                            if (ctx.mounted) {
                              Navigator.pop(ctx);
                              ref.invalidate(perfilProvider);
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content:
                                      Text('Teléfono actualizado correctamente'),
                                  backgroundColor: AppTheme.success,
                                ),
                              );
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
                          width: 20,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : const Text('Guardar teléfono'),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Future<void> _cambiarEmail(
    BuildContext context,
    WidgetRef ref,
    String? pendiente,
  ) async {
    final emailFormKey = GlobalKey<FormState>();
    final codeFormKey = GlobalKey<FormState>();
    final emailCtrl = TextEditingController(text: pendiente ?? '');
    final codeCtrl = TextEditingController();
    bool isLoading = false;
    // Si ya hay un cambio pendiente de una vez anterior, arranca directo en
    // el paso de código en vez de pedir el correo de nuevo.
    bool pidiendoCodigo = pendiente != null && pendiente.isNotEmpty;

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setState) => Padding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(ctx).viewInsets.bottom,
            left: 24,
            right: 24,
            top: 24,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                pidiendoCodigo ? 'Confirma tu correo nuevo' : 'Cambiar correo',
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  color: AppTheme.primary,
                ),
              ),
              const SizedBox(height: 8),
              if (!pidiendoCodigo)
                Form(
                  key: emailFormKey,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      TextFormField(
                        controller: emailCtrl,
                        keyboardType: TextInputType.emailAddress,
                        decoration: const InputDecoration(
                          labelText: 'Correo nuevo',
                          prefixIcon: Icon(Icons.email_outlined),
                        ),
                        validator: (v) {
                          if (v == null || v.isEmpty) return 'Ingresa tu correo';
                          if (!RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$').hasMatch(v)) {
                            return 'Correo inválido';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 20),
                      ElevatedButton(
                        onPressed: isLoading
                            ? null
                            : () async {
                                if (!emailFormKey.currentState!.validate()) return;
                                setState(() => isLoading = true);
                                try {
                                  await ref
                                      .read(perfilRepositoryProvider)
                                      .solicitarCambioEmail(emailCtrl.text.trim());
                                  setState(() {
                                    isLoading = false;
                                    pidiendoCodigo = true;
                                  });
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
                                width: 20,
                                child: CircularProgressIndicator(
                                  color: Colors.white,
                                  strokeWidth: 2,
                                ),
                              )
                            : const Text('Enviar código'),
                      ),
                      const SizedBox(height: 24),
                    ],
                  ),
                )
              else
                Form(
                  key: codeFormKey,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Te enviamos un código de 6 dígitos a ${emailCtrl.text}. Ingrésalo para confirmar el cambio.',
                        style: const TextStyle(
                          fontSize: 13,
                          color: AppTheme.textMuted,
                        ),
                      ),
                      const SizedBox(height: 16),
                      TextFormField(
                        controller: codeCtrl,
                        keyboardType: TextInputType.number,
                        maxLength: 6,
                        decoration: const InputDecoration(
                          labelText: 'Código de verificación',
                          prefixIcon: Icon(Icons.pin_outlined),
                          counterText: '',
                        ),
                        validator: (v) {
                          if (v == null || v.length != 6) {
                            return 'Ingresa el código de 6 dígitos';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 12),
                      ElevatedButton(
                        onPressed: isLoading
                            ? null
                            : () async {
                                if (!codeFormKey.currentState!.validate()) return;
                                setState(() => isLoading = true);
                                try {
                                  final data = await ref
                                      .read(perfilRepositoryProvider)
                                      .confirmarCambioEmail(codeCtrl.text.trim());
                                  final usuarioActualizado =
                                      data['usuario'] as Map<String, dynamic>?;
                                  if (usuarioActualizado != null) {
                                    await ref
                                        .read(authProvider.notifier)
                                        .actualizarUsuario(usuarioActualizado);
                                  }
                                  if (ctx.mounted) {
                                    Navigator.pop(ctx);
                                    ref.invalidate(perfilProvider);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content:
                                            Text('Correo actualizado correctamente'),
                                        backgroundColor: AppTheme.success,
                                      ),
                                    );
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
                                width: 20,
                                child: CircularProgressIndicator(
                                  color: Colors.white,
                                  strokeWidth: 2,
                                ),
                              )
                            : const Text('Confirmar correo'),
                      ),
                      TextButton(
                        onPressed: isLoading
                            ? null
                            : () => setState(() => pidiendoCodigo = false),
                        child: const Text('Usar otro correo'),
                      ),
                      const SizedBox(height: 12),
                    ],
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _confirmarLogout(BuildContext context, WidgetRef ref) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Cerrar sesión'),
        content: const Text('¿Estás seguro de cerrar sesión?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text(
              'Cerrar sesión',
              style: TextStyle(color: AppTheme.error),
            ),
          ),
        ],
      ),
    );
    if (confirm == true) {
      await ref.read(authProvider.notifier).logout();
    }
  }

  Future<void> _cambiarPassword(BuildContext context, WidgetRef ref) async {
    final formKey = GlobalKey<FormState>();
    final currentCtrl = TextEditingController();
    final newCtrl = TextEditingController();
    final confirmCtrl = TextEditingController();
    bool isLoading = false;

    await showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setState) => Padding(
          padding: EdgeInsets.only(
            bottom: MediaQuery.of(ctx).viewInsets.bottom,
            left: 24,
            right: 24,
            top: 24,
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
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.primary,
                  ),
                ),
                const SizedBox(height: 16),
                TextFormField(
                  controller: currentCtrl,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText: 'Contraseña actual',
                    prefixIcon: Icon(Icons.lock_outline),
                  ),
                  validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
                ),
                const SizedBox(height: 12),
                TextFormField(
                  controller: newCtrl,
                  obscureText: true,
                  onChanged: (_) => setState(() {}),
                  decoration: const InputDecoration(
                    labelText: 'Nueva contraseña',
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
                PasswordStrengthMeter(password: newCtrl.text),
                const SizedBox(height: 12),
                TextFormField(
                  controller: confirmCtrl,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText: 'Confirmar nueva contraseña',
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
                            await ref
                                .read(perfilRepositoryProvider)
                                .cambiarPassword(
                                  currentPassword: currentCtrl.text,
                                  newPassword: newCtrl.text,
                                  confirmation: confirmCtrl.text,
                                );
                            if (ctx.mounted) {
                              Navigator.pop(ctx);
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text(
                                      'Contraseña actualizada correctamente'),
                                  backgroundColor: AppTheme.success,
                                ),
                              );
                              // Logout para relogin con nueva contraseña
                              await ref.read(authProvider.notifier).logout();
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
                          width: 20,
                          child: CircularProgressIndicator(
                            color: Colors.white,
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

class _StatCard extends StatelessWidget {
  final String label;
  final String value;
  final IconData icon;
  final Color color;

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
          children: [
            Icon(icon, color: color, size: 24),
            const SizedBox(height: 6),
            Text(
              value,
              style: TextStyle(
                fontSize: 22,
                fontWeight: FontWeight.w700,
                color: color,
              ),
            ),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10,
                color: AppTheme.textMuted,
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
  final String title;
  final List<Widget> children;

  const _InfoCard({required this.title, required this.children});

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
          ...children,
        ],
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;
  final Color? valueColor;
  final String? subtitulo;
  final VoidCallback? onEdit;

  const _InfoRow({
    required this.icon,
    required this.label,
    required this.value,
    this.valueColor,
    this.subtitulo,
    this.onEdit,
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
                    color: AppTheme.textMuted,
                  ),
                ),
                Text(
                  value,
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: valueColor ?? AppTheme.primary,
                  ),
                ),
                if (subtitulo != null)
                  Padding(
                    padding: const EdgeInsets.only(top: 2),
                    child: Text(
                      subtitulo!,
                      style: const TextStyle(
                        fontSize: 11,
                        fontWeight: FontWeight.w600,
                        color: AppTheme.accent,
                      ),
                    ),
                  ),
              ],
            ),
          ),
          if (onEdit != null)
            IconButton(
              icon: const Icon(Icons.edit_outlined, size: 18),
              color: AppTheme.textMuted,
              visualDensity: VisualDensity.compact,
              onPressed: onEdit,
            ),
        ],
      ),
    );
  }
}
