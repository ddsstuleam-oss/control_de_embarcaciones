import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../providers/embarcacion_provider.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/empty_state_widget.dart';
import '../../../../core/widgets/user_avatar.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../../features/auth/providers/auth_provider.dart';

class EmbarcacionesScreen extends ConsumerStatefulWidget {
  const EmbarcacionesScreen({super.key});

  @override
  ConsumerState<EmbarcacionesScreen> createState() =>
      _EmbarcacionesScreenState();
}

class _EmbarcacionesScreenState extends ConsumerState<EmbarcacionesScreen> {
  DateTime _fechaSeleccionada = DateTime.now().add(const Duration(days: 1));

  String get _fechaStr => DateFormat('yyyy-MM-dd').format(_fechaSeleccionada);

  Future<void> _seleccionarFecha() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _fechaSeleccionada,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 90)),
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: const ColorScheme.light(primary: AppTheme.primary),
        ),
        child: child!,
      ),
    );
    if (picked != null) {
      setState(() => _fechaSeleccionada = picked);
    }
  }

  String _etiquetaDia() {
    final hoy = DateTime.now();
    final today = DateTime(hoy.year, hoy.month, hoy.day);
    final sel = DateTime(
      _fechaSeleccionada.year,
      _fechaSeleccionada.month,
      _fechaSeleccionada.day,
    );
    final diff = sel.difference(today).inDays;
    if (diff == 0) return 'Hoy';
    if (diff == 1) return 'Mañana';
    if (diff > 1) return 'En $diff días';
    return '';
  }

  String _capitalizar(String texto) =>
      texto.isEmpty ? texto : texto[0].toUpperCase() + texto.substring(1);

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final disponibilidad = ref.watch(disponibilidadProvider(_fechaStr));

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Embarcaciones'),
        actions: [
          IconButton(
            icon: const Icon(Icons.person_outline),
            onPressed: () => context.push('/perfil'),
          ),
          IconButton(
            icon: const Icon(Icons.list_alt),
            onPressed: () => context.push('/reservas'),
          ),
        ],
      ),
      drawer: _buildDrawer(authState),
      body: WebCentered(
        maxWidth: 480,
        child: Column(
          children: [
            _buildDateHeader(),

            // Lista
            Expanded(
              child: disponibilidad.when(
                loading: () => const Center(
                  child: CircularProgressIndicator(color: AppTheme.primary),
                ),
                error: (e, _) => _buildError(),
                data: (embarcaciones) => embarcaciones.isEmpty
                    ? const EmptyStateWidget(
                        icon: Icons.directions_boat_outlined,
                        titulo: 'Sin embarcaciones',
                        mensaje:
                            'No hay embarcaciones disponibles\npara la fecha seleccionada.',
                      )
                    : RefreshIndicator(
                        onRefresh: () async =>
                            ref.refresh(disponibilidadProvider(_fechaStr)),
                        child: ListView.builder(
                          padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
                          itemCount: embarcaciones.length + 1,
                          itemBuilder: (_, i) {
                            if (i == 0) {
                              return _ResumenRow(embarcaciones: embarcaciones);
                            }
                            return _EmbarcacionCard(
                              embarcacion: embarcaciones[i - 1],
                              fecha: _fechaStr,
                            );
                          },
                        ),
                      ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildDateHeader() {
    final etiqueta = _etiquetaDia();
    return Container(
      width: double.infinity,
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [AppTheme.primary, Color(0xFF0A3D62)],
        ),
        borderRadius: BorderRadius.vertical(bottom: Radius.circular(24)),
        boxShadow: [
          BoxShadow(
              color: Colors.black26, blurRadius: 10, offset: Offset(0, 4)),
        ],
      ),
      padding: const EdgeInsets.fromLTRB(20, 4, 16, 18),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.15),
              borderRadius: BorderRadius.circular(14),
            ),
            child: const Icon(Icons.calendar_month_rounded,
                color: Colors.white, size: 24),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (etiqueta.isNotEmpty)
                  Container(
                    margin: const EdgeInsets.only(bottom: 4),
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: AppTheme.accent,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      etiqueta,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                Text(
                  _capitalizar(
                    DateFormat('EEEE, d MMMM yyyy', 'es')
                        .format(_fechaSeleccionada),
                  ),
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          OutlinedButton.icon(
            onPressed: _seleccionarFecha,
            icon: const Icon(Icons.edit_calendar_outlined,
                size: 16, color: Colors.white),
            label: const Text('Cambiar',
                style: TextStyle(color: Colors.white, fontSize: 12)),
            style: OutlinedButton.styleFrom(
              side: const BorderSide(color: Colors.white38),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              minimumSize: Size.zero,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildError() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: AppTheme.error.withOpacity(0.1),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.error_outline,
                  size: 48, color: AppTheme.error),
            ),
            const SizedBox(height: 16),
            const Text(
              'Error al cargar embarcaciones',
              style: TextStyle(
                fontWeight: FontWeight.w700,
                fontSize: 16,
                color: AppTheme.primary,
              ),
            ),
            const SizedBox(height: 4),
            const Text(
              'Verifica tu conexión e intenta nuevamente.',
              textAlign: TextAlign.center,
              style: TextStyle(color: AppTheme.textMuted, fontSize: 13),
            ),
            const SizedBox(height: 16),
            ElevatedButton.icon(
              onPressed: () => ref.refresh(disponibilidadProvider(_fechaStr)),
              icon: const Icon(Icons.refresh),
              label: const Text('Reintentar'),
              style: ElevatedButton.styleFrom(minimumSize: const Size(160, 46)),
            ),
          ],
        ),
      ),
    );
  }

  Drawer _buildDrawer(AuthState authState) {
    return Drawer(
      child: SafeArea(
        child: Column(
          children: [
            UserAccountsDrawerHeader(
              decoration: const BoxDecoration(color: AppTheme.primary),
              accountName: Text(authState.user?['nombre'] ?? ''),
              accountEmail: Text(authState.user?['email'] ?? ''),
              currentAccountPicture: UserAvatar(
                nombre: authState.user?['nombre'] as String? ?? 'U',
                fotoUrl: authState.user?['foto_url'] as String?,
                radius: 36,
                backgroundColor: AppTheme.accent,
              ),
            ),
            Expanded(
              child: ListView(
                padding: EdgeInsets.zero,
                children: [
                  ListTile(
                    leading: const Icon(Icons.home_outlined),
                    title: const Text('Inicio'),
                    onTap: () {
                      Navigator.pop(context);
                      context.go('/home');
                    },
                  ),
                  ListTile(
                    leading: const Icon(Icons.directions_boat),
                    title: const Text('Embarcaciones'),
                    selected: true,
                    onTap: () => Navigator.pop(context),
                  ),
                  ListTile(
                    leading: const Icon(Icons.book_online),
                    title: const Text('Mis reservas'),
                    onTap: () {
                      Navigator.pop(context);
                      context.push('/reservas');
                    },
                  ),
                  ListTile(
                    leading: const Icon(Icons.person_outline),
                    title: const Text('Mi perfil'),
                    onTap: () {
                      Navigator.pop(context);
                      context.push('/perfil');
                    },
                  ),
                  if (authState.isOperador || authState.isAdmin) ...[
                    const Divider(),
                    ListTile(
                      leading: const Icon(Icons.qr_code_scanner),
                      title: const Text('Escanear QR'),
                      onTap: () {
                        Navigator.pop(context);
                        context.push('/scanner');
                      },
                    ),
                  ],
                  if (authState.isAdmin) ...[
                    const Divider(),
                    ListTile(
                      leading: const Icon(Icons.dashboard_outlined),
                      title: const Text('Dashboard'),
                      onTap: () {
                        Navigator.pop(context);
                        context.push('/admin/dashboard');
                      },
                    ),
                    ListTile(
                      leading: const Icon(Icons.people_outline),
                      title: const Text('Usuarios'),
                      onTap: () {
                        Navigator.pop(context);
                        context.push('/admin/usuarios');
                      },
                    ),
                    ListTile(
                      leading: const Icon(Icons.assessment_outlined),
                      title: const Text('Reportes'),
                      onTap: () {
                        Navigator.pop(context);
                        context.push('/admin/reportes');
                      },
                    ),
                  ],
                ],
              ),
            ),
            const Divider(height: 1),
            ListTile(
              leading: const Icon(Icons.logout, color: AppTheme.error),
              title: const Text('Cerrar sesión',
                  style: TextStyle(color: AppTheme.error)),
              onTap: () async {
                Navigator.pop(context);
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
                if (confirm == true && context.mounted) {
                  await ref.read(authProvider.notifier).logout();
                }
              },
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );
  }
}

// ── Resumen del día ─────────────────────────────────────────────────────────

class _ResumenRow extends StatelessWidget {
  final List<Map<String, dynamic>> embarcaciones;

  const _ResumenRow({required this.embarcaciones});

  @override
  Widget build(BuildContext context) {
    final total = embarcaciones.length;
    final disponibles =
        embarcaciones.where((e) => e['disponible'] == true).length;
    final cupos = embarcaciones.fold<int>(
      0,
      (suma, e) => suma + (e['disponibles'] as int? ?? 0),
    );

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      padding: const EdgeInsets.symmetric(vertical: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: _ResumenItem(
              icon: Icons.directions_boat_rounded,
              value: '$total',
              label: 'Embarcaciones',
              color: AppTheme.primary,
            ),
          ),
          Container(height: 36, width: 1, color: Colors.grey[200]),
          Expanded(
            child: _ResumenItem(
              icon: Icons.check_circle_rounded,
              value: '$disponibles',
              label: 'Disponibles',
              color: AppTheme.success,
            ),
          ),
          Container(height: 36, width: 1, color: Colors.grey[200]),
          Expanded(
            child: _ResumenItem(
              icon: Icons.event_seat_rounded,
              value: '$cupos',
              label: 'Cupos libres',
              color: AppTheme.accent,
            ),
          ),
        ],
      ),
    );
  }
}

class _ResumenItem extends StatelessWidget {
  final IconData icon;
  final String value;
  final String label;
  final Color color;

  const _ResumenItem({
    required this.icon,
    required this.value,
    required this.label,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Icon(icon, color: color, size: 20),
        const SizedBox(height: 4),
        Text(
          value,
          style: TextStyle(
              fontSize: 18, fontWeight: FontWeight.w800, color: color),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: const TextStyle(fontSize: 10, color: AppTheme.textMuted),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }
}

// ── Tarjeta de embarcación ───────────────────────────────────────────────────

class _EmbarcacionCard extends StatefulWidget {
  final Map<String, dynamic> embarcacion;
  final String fecha;

  const _EmbarcacionCard({
    required this.embarcacion,
    required this.fecha,
  });

  @override
  State<_EmbarcacionCard> createState() => _EmbarcacionCardState();
}

class _EmbarcacionCardState extends State<_EmbarcacionCard> {
  bool _infoVisible = false;

  @override
  Widget build(BuildContext context) {
    final embarcacion = widget.embarcacion;
    final nombre = embarcacion['nombre'] as String? ?? '';
    final descripcion = (embarcacion['descripcion'] as String? ?? '').trim();
    final imagenUrl = embarcacion['imagen_url'] as String?;
    final disponibles = embarcacion['disponibles'] as int? ?? 0;
    final capacidad = embarcacion['capacidad'] as int? ?? 0;
    final reservados = embarcacion['reservados'] as int? ?? 0;
    final disponible = embarcacion['disponible'] as bool? ?? false;
    final porcentaje =
        capacidad > 0 ? (reservados / capacidad).clamp(0.0, 1.0) : 0.0;
    final estilo = _estiloPara(nombre, disponible);
    final colorBarra = _colorOcupacion(porcentaje);

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: AppTheme.surface,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.06),
            blurRadius: 14,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: disponible
            ? () => context.push(
                '/reservas/crear?embarcacion_id=${embarcacion['id']}&fecha=${widget.fecha}')
            : null,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Foto de la embarcación
            Stack(
              children: [
                if (imagenUrl != null && imagenUrl.isNotEmpty)
                  CachedNetworkImage(
                    imageUrl: imagenUrl,
                    height: 170,
                    width: double.infinity,
                    fit: BoxFit.cover,
                    memCacheWidth: 800,
                    placeholder: (_, __) => _ImagenFallback(estilo: estilo),
                    errorWidget: (_, __, ___) =>
                        _ImagenFallback(estilo: estilo),
                  )
                else
                  _ImagenFallback(estilo: estilo),
                Positioned(
                  top: 12,
                  right: 12,
                  child: _EstadoBadge(
                      disponible: disponible, disponibles: disponibles),
                ),
              ],
            ),

            // Barra de ocupación
            LinearProgressIndicator(
              value: porcentaje,
              backgroundColor: Colors.grey[200],
              valueColor: AlwaysStoppedAnimation(colorBarra),
              minHeight: 4,
            ),

            // Cuerpo
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    nombre,
                    style: const TextStyle(
                      fontSize: 17,
                      fontWeight: FontWeight.w700,
                      color: AppTheme.primary,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(Icons.people_alt_outlined,
                          size: 14, color: AppTheme.textMuted),
                      const SizedBox(width: 4),
                      Text(
                        'Capacidad: $capacidad pasajeros',
                        style: const TextStyle(
                            fontSize: 12, color: AppTheme.textMuted),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  InkWell(
                    onTap: () => setState(() => _infoVisible = !_infoVisible),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          _infoVisible
                              ? Icons.remove_circle_outline
                              : Icons.add_circle_outline,
                          size: 16,
                          color: AppTheme.info,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          _infoVisible
                              ? 'Ocultar información'
                              : 'Más información',
                          style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: AppTheme.info,
                          ),
                        ),
                      ],
                    ),
                  ),
                  AnimatedSize(
                    duration: const Duration(milliseconds: 200),
                    child: _infoVisible
                        ? Padding(
                            padding: const EdgeInsets.only(top: 6),
                            child: Text(
                              descripcion.isNotEmpty
                                  ? descripcion
                                  : 'Esta embarcación no tiene información adicional registrada.',
                              style: const TextStyle(
                                fontSize: 13,
                                color: AppTheme.textMuted,
                                height: 1.4,
                              ),
                            ),
                          )
                        : const SizedBox(width: double.infinity),
                  ),
                  const SizedBox(height: 14),
                  Row(
                    children: [
                      Expanded(
                        child: _StatItem(
                          icon: Icons.event_seat_outlined,
                          value: '$reservados',
                          label: 'Ocupados',
                        ),
                      ),
                      Container(height: 32, width: 1, color: Colors.grey[200]),
                      Expanded(
                        child: _StatItem(
                          icon: Icons.event_available_outlined,
                          value: '$disponibles',
                          label: 'Disponibles',
                          color: disponible ? AppTheme.success : AppTheme.error,
                        ),
                      ),
                      Container(height: 32, width: 1, color: Colors.grey[200]),
                      Expanded(
                        child: _StatItem(
                          icon: Icons.percent_rounded,
                          value: '${(porcentaje * 100).round()}%',
                          label: 'Ocupación',
                          color: colorBarra,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    width: double.infinity,
                    height: 46,
                    child: ElevatedButton.icon(
                      onPressed: disponible
                          ? () => context.push(
                              '/reservas/crear?embarcacion_id=${embarcacion['id']}&fecha=${widget.fecha}')
                          : null,
                      icon: Icon(disponible
                          ? Icons.confirmation_number_outlined
                          : Icons.event_busy_outlined),
                      label:
                          Text(disponible ? 'Reservar' : 'Sin disponibilidad'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor:
                            disponible ? AppTheme.accent : Colors.grey[300],
                        foregroundColor:
                            disponible ? Colors.white : AppTheme.textMuted,
                        elevation: disponible ? 1 : 0,
                        disabledBackgroundColor: Colors.grey[200],
                        disabledForegroundColor: AppTheme.textMuted,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ImagenFallback extends StatelessWidget {
  final _EstiloEmbarcacion estilo;

  const _ImagenFallback({required this.estilo});

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 170,
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: estilo.gradient,
        ),
      ),
      child: Center(
        child:
            Icon(estilo.icon, size: 64, color: Colors.white.withOpacity(0.85)),
      ),
    );
  }
}

class _StatItem extends StatelessWidget {
  final IconData icon;
  final String value;
  final String label;
  final Color? color;

  const _StatItem({
    required this.icon,
    required this.value,
    required this.label,
    this.color,
  });

  @override
  Widget build(BuildContext context) {
    final c = color ?? AppTheme.primary;
    return Column(
      children: [
        Icon(icon, size: 18, color: c),
        const SizedBox(height: 4),
        Text(value,
            style:
                TextStyle(fontSize: 15, fontWeight: FontWeight.w800, color: c)),
        const SizedBox(height: 2),
        Text(label,
            style: const TextStyle(fontSize: 10, color: AppTheme.textMuted)),
      ],
    );
  }
}

class _EstadoBadge extends StatelessWidget {
  final bool disponible;
  final int disponibles;

  const _EstadoBadge({required this.disponible, required this.disponibles});

  @override
  Widget build(BuildContext context) {
    final color = disponible ? AppTheme.success : AppTheme.error;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.15),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            disponible ? Icons.check_circle : Icons.block,
            size: 12,
            color: color,
          ),
          const SizedBox(width: 4),
          Text(
            disponible ? '$disponibles libres' : 'Lleno',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w700,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}

// ── Estilo según tipo de embarcación ─────────────────────────────────────────

class _EstiloEmbarcacion {
  final IconData icon;
  final List<Color> gradient;

  const _EstiloEmbarcacion(this.icon, this.gradient);
}

_EstiloEmbarcacion _estiloPara(String nombre, bool disponible) {
  if (!disponible) {
    return const _EstiloEmbarcacion(
      Icons.directions_boat_filled_rounded,
      [Color(0xFF9E9E9E), Color(0xFF616161)],
    );
  }

  final n = nombre.toLowerCase();
  if (n.contains('rescate')) {
    return const _EstiloEmbarcacion(
      Icons.health_and_safety_rounded,
      [Color(0xFFE63946), Color(0xFFB71C1C)],
    );
  }
  if (n.contains('investiga')) {
    return const _EstiloEmbarcacion(
      Icons.science_rounded,
      [Color(0xFF1976D2), Color(0xFF0D47A1)],
    );
  }
  if (n.contains('bote')) {
    return const _EstiloEmbarcacion(
      Icons.kayaking_rounded,
      [Color(0xFF00897B), Color(0xFF004D40)],
    );
  }
  return const _EstiloEmbarcacion(
    Icons.directions_boat_filled_rounded,
    [AppTheme.primary, Color(0xFF0A3D62)],
  );
}

Color _colorOcupacion(double porcentaje) {
  if (porcentaje >= 0.9) return AppTheme.error;
  if (porcentaje >= 0.6) return AppTheme.warning;
  return AppTheme.success;
}
