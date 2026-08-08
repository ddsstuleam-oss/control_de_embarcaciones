import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/user_avatar.dart';
import '../../../../core/widgets/profile_photo_picker.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../../features/auth/providers/auth_provider.dart';
import '../../../perfil/providers/perfil_provider.dart';
import 'operador_home_screen.dart';

class OperadorPerfilScreen extends ConsumerWidget {
  const OperadorPerfilScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final perfilAsync = ref.watch(operadorPerfilProvider);
    final subiendoFoto = ref.watch(subiendoFotoPerfilProvider);

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(title: const Text('Mi perfil')),
      body: WebCentered(
        maxWidth: 480,
        child: perfilAsync.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (e, _) => Center(child: Text('Error: $e')),
          data: (perfil) {
            final usuario = perfil['usuario'] as Map<String, dynamic>? ?? {};

            return SingleChildScrollView(
              child: Column(
                children: [
                  // Header
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.fromLTRB(24, 32, 24, 40),
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        colors: [AppTheme.primary, Color(0xFF16213E)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                    child: Column(
                      children: [
                        UserAvatar(
                          nombre: usuario['nombre'] as String? ?? 'O',
                          fotoUrl: usuario['foto_url'] as String?,
                          radius: 48,
                          backgroundColor: Colors.blue[400]!,
                          editable: true,
                          uploading: subiendoFoto,
                          onTap: () => _cambiarFoto(context, ref),
                          roleBadge: Container(
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              color: Colors.blue[300],
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: AppTheme.primary,
                                width: 2,
                              ),
                            ),
                            child: const Icon(
                              Icons.qr_code_scanner,
                              color: Colors.white,
                              size: 14,
                            ),
                          ),
                        ),
                        const SizedBox(height: 16),
                        Text(
                          usuario['nombre'] ?? '',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 22,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 14,
                            vertical: 5,
                          ),
                          decoration: BoxDecoration(
                            color: Colors.blue.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(
                              color: Colors.blue.withOpacity(0.5),
                            ),
                          ),
                          child: const Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.qr_code_scanner,
                                  color: Colors.blue, size: 13),
                              SizedBox(width: 6),
                              Text(
                                'OPERADOR',
                                style: TextStyle(
                                  color: Colors.blue,
                                  fontSize: 11,
                                  fontWeight: FontWeight.w700,
                                  letterSpacing: 1,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),

                  Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        // Info personal
                        _Card(
                          title: 'Información personal',
                          children: [
                            _Row(
                                icon: Icons.badge_outlined,
                                label: 'Cédula',
                                value: usuario['cedula'] ?? ''),
                            _Row(
                                icon: Icons.email_outlined,
                                label: 'Email',
                                value: usuario['email'] ?? '',
                                subtitulo: (usuario['email_pendiente']
                                                as String?)
                                            ?.isNotEmpty ==
                                        true
                                    ? 'Confirmando cambio a: ${usuario['email_pendiente']}'
                                    : null,
                                onEdit: () => _cambiarEmail(
                                  context,
                                  ref,
                                  usuario['email_pendiente'] as String?,
                                )),
                            _Row(
                                icon: Icons.calendar_today_outlined,
                                label: 'Miembro desde',
                                value: usuario['miembro_desde'] ?? ''),
                          ],
                        ),

                        const SizedBox(height: 16),

                        // Seguridad
                        _Card(
                          title: 'Seguridad',
                          children: [
                            _Row(
                              icon: Icons.lock_clock_outlined,
                              label: 'Días para vencer contraseña',
                              value: '${_n(usuario['dias_para_vencer'])} días',
                              valueColor: _n(usuario['dias_para_vencer']) < 10
                                  ? AppTheme.error
                                  : AppTheme.success,
                            ),
                          ],
                        ),

                        const SizedBox(height: 16),

                        // Cambiar contraseña
                        _Card(
                          title: 'Opciones',
                          children: [
                            ListTile(
                              contentPadding: EdgeInsets.zero,
                              leading: const Icon(Icons.lock_outline,
                                  color: AppTheme.primary),
                              title: const Text('Cambiar contraseña'),
                              trailing: const Icon(Icons.chevron_right),
                              onTap: () => _cambiarPassword(context, ref),
                            ),
                            ListTile(
                              contentPadding: EdgeInsets.zero,
                              leading: const Icon(Icons.history_outlined,
                                  color: AppTheme.primary),
                              title: const Text('Actividad reciente'),
                              trailing: const Icon(Icons.chevron_right),
                              onTap: () => context.push('/perfil/actividad'),
                            ),
                          ],
                        ),

                        const SizedBox(height: 16),

                        OutlinedButton.icon(
                          onPressed: () => _confirmarLogout(context, ref),
                          icon: const Icon(Icons.logout, color: AppTheme.error),
                          label: const Text('Cerrar sesión',
                              style: TextStyle(color: AppTheme.error)),
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

  int _n(dynamic v) => (v as num?)?.toInt() ?? 0;

  Future<void> _cambiarFoto(BuildContext context, WidgetRef ref) async {
    final foto = await elegirYRecortarFotoPerfil(context);
    if (foto == null) return;

    ref.read(subiendoFotoPerfilProvider.notifier).state = true;
    try {
      final data =
          await ref.read(perfilRepositoryProvider).subirFotoPerfil(foto);
      final usuario = data['usuario'] as Map<String, dynamic>?;
      if (usuario != null) {
        await ref.read(authProvider.notifier).actualizarUsuario(usuario);
      }
      ref.invalidate(operadorPerfilProvider);
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

  Future<void> _confirmarLogout(BuildContext context, WidgetRef ref) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Cerrar sesión'),
        content: const Text('¿Estás seguro?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Salir', style: TextStyle(color: AppTheme.error)),
          ),
        ],
      ),
    );
    if (confirm == true) {
      await ref.read(authProvider.notifier).logout();
    }
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
                                    ref.invalidate(operadorPerfilProvider);
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
                const Text('Cambiar contraseña',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.primary,
                    )),
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
                  decoration: const InputDecoration(
                    labelText: 'Nueva contraseña',
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
                  controller: confirmCtrl,
                  obscureText: true,
                  decoration: const InputDecoration(
                    labelText: 'Confirmar contraseña',
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
                                'current_password': currentCtrl.text,
                                'new_password': newCtrl.text,
                                'new_password_confirmation': confirmCtrl.text,
                              },
                            );
                            if (ctx.mounted) Navigator.pop(ctx);
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text('Contraseña actualizada'),
                                  backgroundColor: AppTheme.success,
                                ),
                              );
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

class _Card extends StatelessWidget {
  final String title;
  final List<Widget> children;
  const _Card({required this.title, required this.children});

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
          Text(title,
              style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w700,
                color: AppTheme.primary,
              )),
          const SizedBox(height: 12),
          ...children,
        ],
      ),
    );
  }
}

class _Row extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;
  final Color? valueColor;
  final String? subtitulo;
  final VoidCallback? onEdit;
  const _Row(
      {required this.icon,
      required this.label,
      required this.value,
      this.valueColor,
      this.subtitulo,
      this.onEdit});

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
                Text(label,
                    style: const TextStyle(
                      fontSize: 11,
                      color: AppTheme.textMuted,
                    )),
                Text(value,
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: valueColor ?? AppTheme.primary,
                    )),
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
