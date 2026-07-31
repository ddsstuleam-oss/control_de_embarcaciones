import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../utils/responsive.dart';
import 'admin_nav.dart';

/// Envoltorio único para las pantallas del panel admin.
///
/// En móvil (nativo o navegador angosto) se comporta como siempre: `Scaffold`
/// con `AppBar` + [AdminDrawer]. En web con ancho de escritorio, el
/// `Scaffold`/sidebar ya los pone [AdminShell] (montado una sola vez por el
/// `ShellRoute` en `app_router.dart`), así que aquí solo se arma el
/// encabezado de página (título + acciones) y se le da a [body] el mismo
/// alto acotado que tendría como `Scaffold.body`. El breakpoint debe ir en
/// sintonía con el que usa [AdminShell] para decidir si dibuja el sidebar.
class AdminPageScaffold extends StatefulWidget {
  final AdminNavItem   navItem;
  final Widget         body;
  final String?        title;
  final String?        subtitle;
  final List<Widget>?  actions;
  final Widget?        floatingActionButton;

  const AdminPageScaffold({
    super.key,
    required this.navItem,
    required this.body,
    this.title,
    this.subtitle,
    this.actions,
    this.floatingActionButton,
  });

  @override
  State<AdminPageScaffold> createState() => _AdminPageScaffoldState();
}

class _AdminPageScaffoldState extends State<AdminPageScaffold> {
  // Misma idea que en AdminShell: sin esta key, cruzar el breakpoint cambia
  // la forma del árbol (Padding+Column vs Scaffold) y Flutter reconstruye
  // `body` desde cero, perdiendo el estado de cualquier formulario abierto.
  final GlobalKey _bodyKey = GlobalKey();

  @override
  Widget build(BuildContext context) {
    final heading = widget.title ?? widget.navItem.label;
    final useSidebarLayout = kIsWeb && Responsive.isDesktop(context);
    final body = KeyedSubtree(key: _bodyKey, child: widget.body);

    if (useSidebarLayout) {
      return Padding(
        padding: const EdgeInsets.fromLTRB(28, 22, 28, 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        heading,
                        style: const TextStyle(
                          fontSize:   22,
                          fontWeight: FontWeight.w800,
                          color:      AppTheme.primary,
                        ),
                      ),
                      if (widget.subtitle != null) ...[
                        const SizedBox(height: 4),
                        Text(
                          widget.subtitle!,
                          style: const TextStyle(
                            fontSize: 12.5,
                            color:    AppTheme.textMuted,
                          ),
                        ),
                      ],
                    ],
                  ),
                ),
                if (widget.actions != null && widget.actions!.isNotEmpty)
                  Row(mainAxisSize: MainAxisSize.min, children: widget.actions!),
              ],
            ),
            const SizedBox(height: 18),
            Expanded(child: body),
            if (widget.floatingActionButton != null) ...[
              const SizedBox(height: 12),
              Align(alignment: Alignment.centerRight, child: widget.floatingActionButton),
            ],
          ],
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title:   Text(heading),
        actions: widget.actions,
      ),
      drawer: AdminDrawer(active: widget.navItem),
      floatingActionButton: widget.floatingActionButton,
      body: body,
    );
  }
}
