import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../theme/app_theme.dart';
import '../utils/responsive.dart';
import '../../features/auth/providers/auth_provider.dart';
import 'user_avatar.dart';

/// Fuente única de verdad para la navegación del panel admin — la usan tanto
/// el [AdminDrawer] (móvil) como el sidebar de [AdminShell] (web), para que
/// agregar/quitar una sección no implique tocar dos listas.
enum AdminNavItem {
  dashboard(
    route: '/admin/dashboard',
    label: 'Dashboard',
    icon: Icons.dashboard_outlined,
  ),
  reservas(
    route: '/admin/reservas',
    label: 'Reservas',
    icon: Icons.book_online_outlined,
  ),
  embarcaciones(
    route: '/admin/embarcaciones',
    label: 'Embarcaciones',
    icon: Icons.directions_boat_outlined,
  ),
  usuarios(
    route: '/admin/usuarios',
    label: 'Usuarios',
    icon: Icons.people_outline,
  ),
  reportes(
    route: '/admin/reportes',
    label: 'Reportes',
    icon: Icons.assessment_outlined,
  ),
  directorio(
    route: '/admin/directorio',
    label: 'Directorio',
    icon: Icons.folder_shared_outlined,
  ),
  perfil(
    route: '/admin/perfil',
    label: 'Mi perfil',
    icon: Icons.person_outline,
  );

  final String route;
  final String label;
  final IconData icon;

  const AdminNavItem({
    required this.route,
    required this.label,
    required this.icon,
  });
}

// Evita abrir varios diálogos de confirmación apilados si el usuario toca
// el botón más de una vez mientras el primero sigue abierto (p. ej. porque
// el primer toque pareció no responder) — cada "Cancelar"/"Cerrar sesión"
// solo cerraría el diálogo de encima y dejaría los demás debajo, dando la
// sensación de que el botón no hace nada.
bool _cerrandoSesionEnCurso = false;

Future<void> confirmarCerrarSesion(BuildContext context, WidgetRef ref) async {
  if (_cerrandoSesionEnCurso) return;
  _cerrandoSesionEnCurso = true;
  // Capturamos el notifier ya mismo, antes de cualquier `await`: en el
  // Drawer móvil este `ref` pertenece al propio Drawer, que se empieza a
  // cerrar (y termina disposed) mientras el usuario todavía está mirando
  // el diálogo de confirmación — leerlo después de eso revienta con
  // "Cannot use ref after the widget was disposed" en silencio. El
  // notifier en sí no depende del árbol de widgets, así que es seguro
  // guardarlo y usarlo más tarde.
  final authNotifier = ref.read(authProvider.notifier);
  try {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title:   const Text('Cerrar sesión'),
        content: const Text('¿Estás seguro de cerrar sesión?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext, false),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(dialogContext, true),
            child: const Text('Cerrar sesión',
                style: TextStyle(color: AppTheme.error)),
          ),
        ],
      ),
    );
    if (confirm == true) {
      await authNotifier.logout();
    }
  } finally {
    _cerrandoSesionEnCurso = false;
  }
}

/// Drawer clásico para el panel admin en móvil/tablet angosto.
class AdminDrawer extends ConsumerWidget {
  final AdminNavItem active;
  const AdminDrawer({super.key, required this.active});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Drawer(
      child: SafeArea(
        child: Column(
          children: [
            const DrawerHeader(
              decoration: BoxDecoration(color: AppTheme.primary),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment:  MainAxisAlignment.end,
                children: [
                  Icon(Icons.admin_panel_settings,
                      color: Colors.white, size: 36),
                  SizedBox(height: 8),
                  Text(
                    'Panel Admin',
                    style: TextStyle(
                      color:      Colors.white,
                      fontSize:   20,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  Text(
                    'FCVT — ULEAM',
                    style: TextStyle(color: Colors.white70, fontSize: 12),
                  ),
                ],
              ),
            ),
            Expanded(
              child: ListView(
                padding: EdgeInsets.zero,
                children: [
                  for (final item in AdminNavItem.values)
                    ListTile(
                      leading:       Icon(item.icon),
                      title:         Text(item.label),
                      selected:      item == active,
                      selectedColor: AppTheme.primary,
                      onTap: () {
                        Navigator.pop(context);
                        if (item != active) context.push(item.route);
                      },
                    ),
                  ListTile(
                    leading: const Icon(Icons.qr_code_scanner),
                    title:   const Text('Escanear QR'),
                    onTap: () {
                      Navigator.pop(context);
                      context.push('/scanner');
                    },
                  ),
                ],
              ),
            ),
            const Divider(height: 1),
            ListTile(
              leading: const Icon(Icons.logout, color: AppTheme.error),
              title: const Text(
                'Cerrar sesión',
                style: TextStyle(color: AppTheme.error),
              ),
              onTap: () async {
                Navigator.pop(context);
                await confirmarCerrarSesion(context, ref);
              },
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );
  }
}

/// Shell persistente para el panel admin en web: sidebar fijo + topbar,
/// usado como `builder` de un `ShellRoute` que envuelve las rutas /admin/*
/// solo quando `kIsWeb` (en móvil se sigue usando [AdminDrawer] por pantalla).
class AdminShell extends ConsumerStatefulWidget {
  final Widget child;
  final String location;

  const AdminShell({super.key, required this.child, required this.location});

  @override
  ConsumerState<AdminShell> createState() => _AdminShellState();
}

class _AdminShellState extends ConsumerState<AdminShell> {
  // GlobalKey estable: si el sidebar cambia a Drawer (o viceversa) al cruzar
  // el breakpoint, la estructura del árbol es distinta y Flutter destruiría
  // y reconstruiría `child` desde cero — perdiendo el estado de cualquier
  // formulario a mitad de llenar. Con la misma key en ambas ramas, Flutter
  // reubica el elemento existente en vez de recrearlo.
  final GlobalKey _contentKey = GlobalKey();

  @override
  Widget build(BuildContext context) {
    final content = KeyedSubtree(key: _contentKey, child: widget.child);

    // El sidebar fijo solo tiene sentido en anchos de escritorio. En un
    // navegador móvil `kIsWeb` también es `true`, así que si no chequeamos
    // el ancho real aquí, un celular termina viendo el mismo sidebar de
    // 248px que una laptop — dejando ~100px para el contenido. Por debajo
    // del breakpoint dejamos pasar el contenido tal cual: cada pantalla ya
    // arma su propio Scaffold + AppBar + Drawer vía [AdminPageScaffold], el
    // mismo patrón que usa la app nativa.
    if (!Responsive.isDesktop(context)) {
      return content;
    }

    final authState = ref.watch(authProvider);
    final nombre  = authState.user?['nombre']   as String? ?? 'Admin';
    final email   = authState.user?['email']    as String? ?? '';
    final fotoUrl = authState.user?['foto_url'] as String?;

    return Scaffold(
      backgroundColor: AppTheme.background,
      body: Row(
        children: [
          _Sidebar(location: widget.location, nombre: nombre, email: email, fotoUrl: fotoUrl),
          Expanded(
            child: ClipRect(child: content),
          ),
        ],
      ),
    );
  }
}

class _Sidebar extends ConsumerWidget {
  final String location;
  final String nombre;
  final String email;
  final String? fotoUrl;

  const _Sidebar({
    required this.location,
    required this.nombre,
    required this.email,
    required this.fotoUrl,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Container(
      width: 248,
      color: AppTheme.primary,
      padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(8, 4, 8, 20),
            child: Row(
              children: [
                Container(
                  width: 38,
                  height: 38,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(10),
                    gradient: const LinearGradient(
                      begin: Alignment.topLeft,
                      end:   Alignment.bottomRight,
                      colors: [AppTheme.accent, Color(0xFFFF6B76)],
                    ),
                  ),
                  child: const Icon(Icons.anchor, color: Colors.white, size: 20),
                ),
                const SizedBox(width: 10),
                const Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Panel Admin',
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 14,
                              fontWeight: FontWeight.w700)),
                      Text('FCVT · ULEAM',
                          style: TextStyle(color: Color(0xFF8688A0), fontSize: 11)),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                _sectionLabel('General'),
                _navTile(context, AdminNavItem.dashboard),
                _navTile(context, AdminNavItem.reportes),
                _sectionLabel('Operación'),
                _navTile(context, AdminNavItem.embarcaciones),
                _navTile(context, AdminNavItem.reservas),
                _customTile(
                  context,
                  icon: Icons.qr_code_scanner,
                  label: 'Escanear QR',
                  selected: location.startsWith('/scanner'),
                  onTap: () => context.push('/scanner'),
                ),
                _sectionLabel('Administración'),
                _navTile(context, AdminNavItem.usuarios),
                _navTile(context, AdminNavItem.directorio),
              ],
            ),
          ),
          const Divider(color: Color(0xFF32334A), height: 1),
          const SizedBox(height: 8),
          Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(10),
              onTap: () => context.push(AdminNavItem.perfil.route),
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 6),
                child: Row(
                  children: [
                    UserAvatar(nombre: nombre, fotoUrl: fotoUrl, radius: 17),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(nombre,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 12.5,
                                  fontWeight: FontWeight.w600)),
                          const Text('Administrador',
                              style: TextStyle(color: Color(0xFF8688A0), fontSize: 10.5)),
                        ],
                      ),
                    ),
                    const Icon(Icons.chevron_right, color: Color(0xFF6D6F8A), size: 18),
                  ],
                ),
              ),
            ),
          ),
          const SizedBox(height: 6),
          Material(
            color: AppTheme.error.withOpacity(0.12),
            borderRadius: BorderRadius.circular(9),
            child: InkWell(
              borderRadius: BorderRadius.circular(9),
              onTap: () => confirmarCerrarSesion(context, ref),
              child: const Padding(
                padding: EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                child: Row(
                  children: [
                    Icon(Icons.logout, color: AppTheme.error, size: 18),
                    SizedBox(width: 11),
                    Text(
                      'Cerrar sesión',
                      style: TextStyle(
                        color:      AppTheme.error,
                        fontSize:   13.5,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _sectionLabel(String text) => Padding(
        padding: const EdgeInsets.fromLTRB(12, 14, 12, 6),
        child: Text(
          text.toUpperCase(),
          style: const TextStyle(
            color: Color(0xFF6D6F8A),
            fontSize: 10.5,
            fontWeight: FontWeight.w700,
            letterSpacing: 0.8,
          ),
        ),
      );

  Widget _navTile(BuildContext context, AdminNavItem item) {
    final selected = location.startsWith(item.route);
    return _customTile(
      context,
      icon: item.icon,
      label: item.label,
      selected: selected,
      onTap: () {
        if (!selected) context.go(item.route);
      },
    );
  }

  Widget _customTile(
    BuildContext context, {
    required IconData icon,
    required String label,
    required bool selected,
    required VoidCallback onTap,
  }) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 2),
      child: Material(
        color: selected ? AppTheme.accent.withOpacity(0.16) : Colors.transparent,
        borderRadius: BorderRadius.circular(9),
        child: InkWell(
          borderRadius: BorderRadius.circular(9),
          onTap: onTap,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 9),
            child: Row(
              children: [
                Icon(icon,
                    size: 18,
                    color: selected ? Colors.white : const Color(0xFFC7C9D9)),
                const SizedBox(width: 11),
                Text(
                  label,
                  style: TextStyle(
                    fontSize: 13.5,
                    fontWeight: selected ? FontWeight.w700 : FontWeight.w500,
                    color: selected ? Colors.white : const Color(0xFFC7C9D9),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

