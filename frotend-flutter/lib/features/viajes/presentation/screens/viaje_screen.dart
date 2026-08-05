import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/utils/file_saver.dart';
import '../../../auth/providers/auth_provider.dart';
import '../../providers/viaje_provider.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../reservas/presentation/screens/crear_reserva_screen.dart'
    show kFacultadesCarreras, facultadDeCarrera, tipoLabel;

class ViajeScreen extends ConsumerStatefulWidget {
  final int id;
  const ViajeScreen({super.key, required this.id});

  @override
  ConsumerState<ViajeScreen> createState() => _ViajeScreenState();
}

class _ViajeScreenState extends ConsumerState<ViajeScreen> {
  bool _procesando = false;

  // Controla si el checklist ya guardado se vuelve a mostrar editable
  // (el usuario tocó "Editar checklist" porque se equivocó).
  bool _editandoEmbarque = false;
  bool _editandoLlegada = false;

  final Map<int, bool> _embarqueOverride = {};
  final Map<int, TextEditingController> _embarqueObs = {};
  final Map<int, bool> _llegadaOverride = {};
  final Map<int, TextEditingController> _llegadaObs = {};

  @override
  void dispose() {
    for (final c in _embarqueObs.values) {
      c.dispose();
    }
    for (final c in _llegadaObs.values) {
      c.dispose();
    }
    super.dispose();
  }

  void _snack(String mensaje, Color color) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(mensaje), backgroundColor: color),
    );
  }

  void _refrescar() => ref.invalidate(viajeDetalleProvider(widget.id));

  bool _abordoValue(Map<String, dynamic> p) =>
      _embarqueOverride[p['id'] as int] ?? (p['presente'] as bool? ?? true);

  bool? _llegoValue(Map<String, dynamic> p) =>
      _llegadaOverride[p['id'] as int] ?? (p['llego'] as bool?);

  TextEditingController _obsController(
    Map<int, TextEditingController> map,
    int id,
    String? initial,
  ) =>
      map.putIfAbsent(id, () => TextEditingController(text: initial ?? ''));

  // ── Acciones ────────────────────────────────────────────────────────────

  Future<void> _guardarChecklistEmbarque(List<dynamic> pasajeros) async {
    setState(() => _procesando = true);
    try {
      final payload = pasajeros.map((p) {
        final m = p as Map<String, dynamic>;
        final id = m['id'] as int;
        return {
          'id': id,
          'abordo': _abordoValue(m),
          'observaciones':
              _obsController(_embarqueObs, id, m['observaciones_embarque'])
                  .text
                  .trim(),
        };
      }).toList();

      await ref
          .read(viajeRepositoryProvider)
          .checklistEmbarque(widget.id, payload);
      _refrescar();
      _snack('Checklist de embarque guardado', AppTheme.success);
      if (mounted) setState(() => _editandoEmbarque = false);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _guardarChecklistLlegada(List<dynamic> pasajeros) async {
    setState(() => _procesando = true);
    try {
      final payload = pasajeros
          .where((p) => (p as Map<String, dynamic>)['presente'] == true)
          .map((p) {
        final m = p as Map<String, dynamic>;
        final id = m['id'] as int;
        return {
          'id': id,
          'llego': _llegoValue(m) ?? false,
          'observaciones':
              _obsController(_llegadaObs, id, m['observaciones_llegada'])
                  .text
                  .trim(),
        };
      }).toList();

      if (payload.isEmpty) {
        _snack('No hay pasajeros que hayan abordado', AppTheme.error);
        return;
      }

      await ref
          .read(viajeRepositoryProvider)
          .checklistLlegada(widget.id, payload);
      _refrescar();
      _snack('Checklist de llegada guardado', AppTheme.success);
      if (mounted) setState(() => _editandoLlegada = false);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _elegirFuenteFotos(String tipo) async {
    final origen = await showModalBottomSheet<ImageSource>(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (_) => SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const SizedBox(height: 10),
            Container(
              width: 40,
              height: 4,
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(height: 8),
            ListTile(
              leading: const Icon(Icons.camera_alt_outlined,
                  color: AppTheme.primary),
              title: const Text('Tomar fotografía'),
              onTap: () => Navigator.pop(context, ImageSource.camera),
            ),
            ListTile(
              leading: const Icon(Icons.photo_library_outlined,
                  color: AppTheme.primary),
              title: const Text('Seleccionar desde la galería'),
              onTap: () => Navigator.pop(context, ImageSource.gallery),
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );

    if (origen == null) return;

    final picker = ImagePicker();
    List<XFile> files;
    if (origen == ImageSource.camera) {
      final foto = await picker.pickImage(
          source: ImageSource.camera, maxWidth: 1280, imageQuality: 80);
      files = foto != null ? [foto] : [];
    } else {
      files = await picker.pickMultiImage(maxWidth: 1280, imageQuality: 80);
    }

    if (files.isEmpty) return;
    await _subirFotos(tipo, files);
  }

  Future<void> _subirFotos(String tipo, List<XFile> files) async {
    setState(() => _procesando = true);
    try {
      await ref.read(viajeRepositoryProvider).subirEvidencia(
            widget.id,
            tipo,
            files,
          );
      _refrescar();
      _snack('Evidencia subida correctamente', AppTheme.success);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _eliminarEvidencia(int evidenciaId) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Eliminar foto'),
        content: const Text('¿Eliminar esta foto de evidencia?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancelar'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text('Eliminar', style: TextStyle(color: AppTheme.error)),
          ),
        ],
      ),
    );
    if (confirm != true) return;

    setState(() => _procesando = true);
    try {
      await ref
          .read(viajeRepositoryProvider)
          .eliminarEvidencia(widget.id, evidenciaId);
      _refrescar();
      _snack('Evidencia eliminada', AppTheme.success);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _confirmarSalida(bool puedeConfirmarSalida) async {
    if (!puedeConfirmarSalida) {
      _snack(
        'Completa y guarda el checklist de embarque antes de confirmar la salida.',
        AppTheme.error,
      );
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Confirmar salida'),
        content: const Text(
            '¿Confirmar la salida del viaje? Se registrará la hora real de salida y el boleto quedará marcado como usado.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('No')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.success),
            child: const Text('Sí, confirmar'),
          ),
        ],
      ),
    );
    if (confirm != true) return;

    setState(() => _procesando = true);
    try {
      await ref.read(viajeRepositoryProvider).iniciar(widget.id);
      _refrescar();
      _snack('Salida confirmada', AppTheme.success);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _confirmarLlegada(bool puedeConfirmarLlegada) async {
    if (!puedeConfirmarLlegada) {
      _snack(
        'Completa y guarda el checklist de llegada antes de confirmar la llegada.',
        AppTheme.error,
      );
      return;
    }

    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Registrar llegada'),
        content: const Text('¿Registrar la hora real de llegada del viaje?'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('No')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.success),
            child: const Text('Sí, registrar'),
          ),
        ],
      ),
    );
    if (confirm != true) return;

    setState(() => _procesando = true);
    try {
      await ref.read(viajeRepositoryProvider).registrarLlegada(widget.id);
      _refrescar();
      _snack('Llegada registrada', AppTheme.success);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<String?> _pedirObservacionesCierre(int pendientes) async {
    final controller = TextEditingController();
    final formKey = GlobalKey<FormState>();

    return showDialog<String>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Cerrar viaje con pasajeros pendientes'),
        content: Form(
          key: formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                  'Hay $pendientes pasajero(s) que abordaron y no han sido confirmados como llegados. '
                  'Como administrador, debes justificar el cierre del viaje.'),
              const SizedBox(height: 16),
              TextFormField(
                controller: controller,
                autofocus: true,
                maxLines: 3,
                decoration: const InputDecoration(
                  labelText: 'Observaciones de cierre',
                  border: OutlineInputBorder(),
                ),
                validator: (v) => v == null || v.trim().isEmpty
                    ? 'Las observaciones son requeridas'
                    : null,
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Cancelar')),
          ElevatedButton(
            onPressed: () {
              if (formKey.currentState!.validate()) {
                Navigator.pop(context, controller.text.trim());
              }
            },
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            child: const Text('Cerrar viaje'),
          ),
        ],
      ),
    );
  }

  Future<void> _finalizar(List<dynamic> pasajeros) async {
    final pendientes = pasajeros.where((p) {
      final m = p as Map<String, dynamic>;
      return m['presente'] == true && m['llego'] != true;
    }).toList();

    String? observaciones;

    if (pendientes.isNotEmpty) {
      final isAdmin = ref.read(authProvider).isAdmin;
      if (!isAdmin) {
        _snack(
          'Hay ${pendientes.length} pasajero(s) sin confirmar llegada. Solo un administrador puede cerrar el viaje en este caso.',
          AppTheme.error,
        );
        return;
      }

      observaciones = await _pedirObservacionesCierre(pendientes.length);
      if (observaciones == null || observaciones.trim().isEmpty) return;
    } else {
      final confirm = await showDialog<bool>(
        context: context,
        builder: (_) => AlertDialog(
          title: const Text('Finalizar viaje'),
          content: const Text(
              '¿Finalizar el viaje? Esta acción no se puede deshacer.'),
          actions: [
            TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('No')),
            ElevatedButton(
              onPressed: () => Navigator.pop(context, true),
              style:
                  ElevatedButton.styleFrom(backgroundColor: AppTheme.success),
              child: const Text('Sí, finalizar'),
            ),
          ],
        ),
      );
      if (confirm != true) return;
    }

    setState(() => _procesando = true);
    try {
      await ref
          .read(viajeRepositoryProvider)
          .finalizar(widget.id, observaciones: observaciones);
      _refrescar();
      _snack('Viaje finalizado correctamente', AppTheme.success);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _descargarReporte() async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Generando reporte...')),
      );
      final bytes = await ref
          .read(viajeRepositoryProvider)
          .descargarReportePdfBytes(widget.id);
      await guardarYAbrirBytes(bytes, 'reporte_viaje_${widget.id}.pdf');
    } catch (e) {
      _snack('Error al descargar el reporte: $e', AppTheme.error);
    }
  }

  Future<void> _abrirFormularioPasajero(
    Map<String, dynamic> data, {
    Map<String, dynamic>? existente,
  }) async {
    final datos = await showModalBottomSheet<Map<String, String>>(
      context: context,
      isScrollControlled: true,
      builder: (_) => _PasajeroFormSheet(existente: existente),
    );
    if (datos == null) return;

    final reservaId = data['reserva']['id'] as int;

    setState(() => _procesando = true);
    try {
      if (existente == null) {
        await ref.read(viajeRepositoryProvider).agregarPasajero(
              reservaId,
              nombre: datos['nombre']!,
              cedula: datos['cedula']!,
              tipo: datos['tipo']!,
              carrera: datos['carrera'],
              facultad: datos['facultad'],
              telefono: datos['telefono'],
              email: datos['email'],
            );
      } else {
        await ref.read(viajeRepositoryProvider).editarPasajero(
              reservaId,
              existente['id'] as int,
              nombre: datos['nombre']!,
              cedula: datos['cedula']!,
              tipo: datos['tipo']!,
              carrera: datos['carrera'],
              facultad: datos['facultad'],
              telefono: datos['telefono'],
              email: datos['email'],
            );
      }
      _refrescar();
      _snack(
        existente == null ? 'Pasajero agregado' : 'Pasajero actualizado',
        AppTheme.success,
      );
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  Future<void> _eliminarPasajero(
      Map<String, dynamic> data, Map<String, dynamic> p) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Quitar pasajero'),
        content: Text('¿Quitar a ${p['nombre']} de esta reserva?'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('No')),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.error),
            child: const Text('Sí, quitar'),
          ),
        ],
      ),
    );
    if (confirm != true) return;

    final reservaId = data['reserva']['id'] as int;

    setState(() => _procesando = true);
    try {
      await ref
          .read(viajeRepositoryProvider)
          .eliminarPasajero(reservaId, p['id'] as int);
      _refrescar();
      _snack('Pasajero eliminado', AppTheme.success);
    } catch (e) {
      _snack(e.toString().replaceAll('Exception: ', ''), AppTheme.error);
    } finally {
      if (mounted) setState(() => _procesando = false);
    }
  }

  // ── Build ───────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    final viajeAsync = ref.watch(viajeDetalleProvider(widget.id));

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(
        title: const Text('Control operativo del viaje'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _refrescar,
          ),
        ],
      ),
      body: WebCentered(
        maxWidth: 560,
        child: viajeAsync.when(
          loading: () => const Center(
            child: CircularProgressIndicator(color: AppTheme.primary),
          ),
          error: (e, _) => Center(
            child: Padding(
              padding: const EdgeInsets.all(24),
              child: Text(
                e.toString().replaceAll('Exception: ', ''),
                textAlign: TextAlign.center,
              ),
            ),
          ),
          data: (data) => Stack(
            children: [
              _buildBody(data),
              if (_procesando)
                Container(
                  color: Colors.black.withOpacity(0.15),
                  child: const Center(
                    child: CircularProgressIndicator(color: AppTheme.primary),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBody(Map<String, dynamic> data) {
    final viaje = data['viaje'] as Map<String, dynamic>;
    final embarcacion = data['embarcacion'] as Map<String, dynamic>;
    final responsable = data['responsable'] as Map<String, dynamic>;
    final operador = data['operador'] as Map<String, dynamic>;
    final reserva = data['reserva'] as Map<String, dynamic>;
    final pasajeros = data['pasajeros'] as List<dynamic>;
    final evidencias = data['evidencias'] as List<dynamic>;
    final estado = viaje['estado'] as String;

    final authState = ref.watch(authProvider);
    final puedeEditarPasajeros =
        estado == 'abordando' && (authState.isOperador || authState.isAdmin);

    final capacidad = embarcacion['capacidad'] as int? ?? pasajeros.length;
    final hayCupo = pasajeros.length < capacidad;

    final evidenciasSalida =
        evidencias.where((e) => (e as Map)['tipo'] == 'salida').toList();
    final evidenciasLlegada =
        evidencias.where((e) => (e as Map)['tipo'] == 'llegada').toList();

    final checklistEmbarqueCompleto =
        viaje['checklist_embarque_completo'] as bool? ?? false;
    final checklistLlegadaCompleto =
        viaje['checklist_llegada_completo'] as bool? ?? false;
    final embarqueEditable = estado == 'abordando' &&
        (!checklistEmbarqueCompleto || _editandoEmbarque);
    final llegadaEditable =
        estado == 'en_curso' && (!checklistLlegadaCompleto || _editandoLlegada);
    // Mientras el checklist esté en modo edición (aunque ya se haya
    // guardado antes) no se puede confirmar salida/llegada: hay que
    // guardar los cambios primero para no dejar overrides sin persistir.
    final puedeConfirmarSalida = checklistEmbarqueCompleto && !_editandoEmbarque;
    final puedeConfirmarLlegada = checklistLlegadaCompleto && !_editandoLlegada;

    return RefreshIndicator(
      onRefresh: () async => _refrescar(),
      child: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _Header(
            embarcacionNombre: embarcacion['nombre'] ?? '',
            responsableNombre: responsable['nombre'] ?? '',
            responsableTelefono: responsable['telefono'] as String?,
            operadorNombre: operador['nombre'] ?? '',
            fecha: reserva['fecha'] ?? '',
            horaProgSalida: viaje['hora_programada_salida'],
            horaProgLlegada: viaje['hora_programada_llegada'],
            horaRealSalida: viaje['hora_real_salida'],
            horaRealLlegada: viaje['hora_real_llegada'],
            estado: estado,
          ),
          const SizedBox(height: 16),

          // ── Pasajeros ────────────────────────────────────────
          _SectionCard(
            title: 'Pasajeros (${pasajeros.length}/$capacidad)',
            trailing: puedeEditarPasajeros
                ? TextButton.icon(
                    onPressed:
                        hayCupo ? () => _abrirFormularioPasajero(data) : null,
                    icon: const Icon(Icons.person_add_alt_1, size: 18),
                    label: const Text('Agregar'),
                  )
                : null,
            child: Column(
              children: pasajeros.map((p) {
                final m = p as Map<String, dynamic>;
                return ListTile(
                  contentPadding: EdgeInsets.zero,
                  title: Text(
                    m['nombre'] ?? '',
                    style: const TextStyle(
                        fontWeight: FontWeight.w600, fontSize: 14),
                  ),
                  subtitle: Text(
                    'CI: ${m['cedula'] ?? ''}'
                    '${m['tipo'] != null ? ' · ${tipoLabel(m['tipo'] as String)}' : ''}'
                    '${(m['carrera'] as String?)?.isNotEmpty == true ? ' · ${m['carrera']}' : ''}',
                    style: const TextStyle(fontSize: 12),
                  ),
                  trailing: puedeEditarPasajeros
                      ? Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            IconButton(
                              icon: const Icon(Icons.edit_outlined, size: 18),
                              color: AppTheme.primary,
                              onPressed: () =>
                                  _abrirFormularioPasajero(data, existente: m),
                            ),
                            IconButton(
                              icon: const Icon(Icons.person_remove_outlined,
                                  size: 18),
                              color: AppTheme.error,
                              onPressed: () => _eliminarPasajero(data, m),
                            ),
                          ],
                        )
                      : null,
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 16),

          // ── Checklist embarque ─────────────────────────────────
          _ChecklistSection(
            titulo: 'Checklist de embarque',
            subtitulo: '¿Quién abordó?',
            pasajeros: pasajeros,
            soloLectura: !embarqueEditable,
            valorActual: (p) => _abordoValue(p),
            onCambiar: embarqueEditable
                ? (id, v) => setState(() => _embarqueOverride[id] = v)
                : null,
            obsController: embarqueEditable
                ? (id, initial) => _obsController(_embarqueObs, id, initial)
                : null,
            obsKey: 'observaciones_embarque',
            contadorLabel: (marcados, total) =>
                '$marcados de $total pasajeros abordaron',
            onGuardar: embarqueEditable
                ? () => _guardarChecklistEmbarque(pasajeros)
                : null,
          ),
          if (estado == 'abordando' &&
              checklistEmbarqueCompleto &&
              !_editandoEmbarque)
            Padding(
              padding: const EdgeInsets.only(top: 8),
              child: OutlinedButton.icon(
                onPressed: () => setState(() => _editandoEmbarque = true),
                icon: const Icon(Icons.edit_outlined, size: 18),
                label: const Text('Editar checklist'),
                style: OutlinedButton.styleFrom(
                  minimumSize: const Size(double.infinity, 44),
                ),
              ),
            ),
          const SizedBox(height: 16),

          // ── Evidencia de salida ──────────────────────────────
          if (estado == 'abordando') ...[
            _EvidenciaSection(
              titulo: 'Evidencia de salida',
              fotos: evidenciasSalida,
              onAgregar: () => _elegirFuenteFotos('salida'),
              onEliminar: _eliminarEvidencia,
            ),
            const SizedBox(height: 16),
            ElevatedButton.icon(
              onPressed: evidenciasSalida.isEmpty
                  ? null
                  : () => _confirmarSalida(puedeConfirmarSalida),
              icon: const Icon(Icons.directions_boat),
              label: const Text('Confirmar salida'),
              style: ElevatedButton.styleFrom(
                minimumSize: const Size(double.infinity, 48),
                backgroundColor: AppTheme.success,
              ),
            ),
            if (evidenciasSalida.isEmpty)
              const Padding(
                padding: EdgeInsets.only(top: 6),
                child: Text(
                  'Sube al menos una foto de evidencia para confirmar la salida.',
                  style: TextStyle(fontSize: 11, color: AppTheme.textMuted),
                ),
              ),
            if (!checklistEmbarqueCompleto)
              const Padding(
                padding: EdgeInsets.only(top: 6),
                child: Text(
                  'Completa el checklist de embarque para poder confirmar la salida.',
                  style: TextStyle(fontSize: 11, color: AppTheme.textMuted),
                ),
              )
            else if (_editandoEmbarque)
              const Padding(
                padding: EdgeInsets.only(top: 6),
                child: Text(
                  'Tienes cambios sin guardar en el checklist de embarque. '
                  'Guárdalos antes de confirmar la salida.',
                  style: TextStyle(
                      fontSize: 11,
                      color: AppTheme.warning,
                      fontWeight: FontWeight.w600),
                ),
              ),
            const SizedBox(height: 24),
          ],

          // ── En curso: checklist + evidencia de llegada ────────
          if (estado == 'en_curso' || estado == 'finalizado') ...[
            _ChecklistSection(
              titulo: 'Checklist de llegada',
              subtitulo: '¿Quién llegó?',
              pasajeros: pasajeros
                  .where((p) => (p as Map)['presente'] == true)
                  .toList(),
              soloLectura: !llegadaEditable,
              valorActual: (p) => _llegoValue(p) ?? false,
              onCambiar: llegadaEditable
                  ? (id, v) => setState(() => _llegadaOverride[id] = v)
                  : null,
              obsController: llegadaEditable
                  ? (id, initial) => _obsController(_llegadaObs, id, initial)
                  : null,
              obsKey: 'observaciones_llegada',
              contadorLabel: (marcados, total) =>
                  '$marcados de $total pasajeros llegaron',
              onGuardar: llegadaEditable
                  ? () => _guardarChecklistLlegada(pasajeros)
                  : null,
            ),
            if (estado == 'en_curso' &&
                checklistLlegadaCompleto &&
                !_editandoLlegada)
              Padding(
                padding: const EdgeInsets.only(top: 8),
                child: OutlinedButton.icon(
                  onPressed: () => setState(() => _editandoLlegada = true),
                  icon: const Icon(Icons.edit_outlined, size: 18),
                  label: const Text('Editar checklist'),
                  style: OutlinedButton.styleFrom(
                    minimumSize: const Size(double.infinity, 44),
                  ),
                ),
              ),
            const SizedBox(height: 16),
          ],

          if (estado == 'en_curso') ...[
            _EvidenciaSection(
              titulo: 'Evidencia de llegada',
              fotos: evidenciasLlegada,
              onAgregar: () => _elegirFuenteFotos('llegada'),
              onEliminar: _eliminarEvidencia,
            ),
            const SizedBox(height: 16),
            OutlinedButton.icon(
              onPressed: evidenciasLlegada.isEmpty
                  ? null
                  : () => _confirmarLlegada(puedeConfirmarLlegada),
              icon: const Icon(Icons.flag_outlined),
              label: Text(viaje['hora_real_llegada'] != null
                  ? 'Llegada registrada'
                  : 'Registrar llegada'),
              style: OutlinedButton.styleFrom(
                minimumSize: const Size(double.infinity, 48),
              ),
            ),
            if (evidenciasLlegada.isEmpty)
              const Padding(
                padding: EdgeInsets.only(top: 6, bottom: 8),
                child: Text(
                  'Sube al menos una foto de evidencia para registrar la llegada.',
                  style: TextStyle(fontSize: 11, color: AppTheme.textMuted),
                ),
              ),
            if (!checklistLlegadaCompleto)
              const Padding(
                padding: EdgeInsets.only(top: 6, bottom: 8),
                child: Text(
                  'Completa el checklist de llegada para poder registrarla.',
                  style: TextStyle(fontSize: 11, color: AppTheme.textMuted),
                ),
              )
            else if (_editandoLlegada)
              const Padding(
                padding: EdgeInsets.only(top: 6, bottom: 8),
                child: Text(
                  'Tienes cambios sin guardar en el checklist de llegada. '
                  'Guárdalos antes de registrar la llegada.',
                  style: TextStyle(
                      fontSize: 11,
                      color: AppTheme.warning,
                      fontWeight: FontWeight.w600),
                ),
              ),
            const SizedBox(height: 16),
            ElevatedButton.icon(
              onPressed: viaje['hora_real_llegada'] == null
                  ? null
                  : () => _finalizar(pasajeros),
              icon: const Icon(Icons.check_circle_outline),
              label: const Text('Finalizar viaje'),
              style: ElevatedButton.styleFrom(
                minimumSize: const Size(double.infinity, 48),
                backgroundColor: AppTheme.primary,
              ),
            ),
            const SizedBox(height: 24),
          ],

          // ── Finalizado ────────────────────────────────────────
          if (estado == 'finalizado') ...[
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppTheme.info.withOpacity(0.08),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppTheme.info.withOpacity(0.3)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Viaje finalizado',
                    style: TextStyle(
                        fontWeight: FontWeight.w700, color: AppTheme.info),
                  ),
                  if (viaje['observaciones_cierre'] != null) ...[
                    const SizedBox(height: 6),
                    Text('Observaciones: ${viaje['observaciones_cierre']}'),
                  ],
                ],
              ),
            ),
            const SizedBox(height: 16),
            ElevatedButton.icon(
              onPressed: _descargarReporte,
              icon: const Icon(Icons.picture_as_pdf),
              label: const Text('Descargar reporte PDF'),
              style: ElevatedButton.styleFrom(
                minimumSize: const Size(double.infinity, 48),
              ),
            ),
            const SizedBox(height: 24),
          ],
        ],
      ),
    );
  }
}

// ── Widgets auxiliares ───────────────────────────────────────────────────

class _Header extends StatelessWidget {
  final String embarcacionNombre;
  final String responsableNombre;
  final String? responsableTelefono;
  final String operadorNombre;
  final String fecha;
  final String? horaProgSalida;
  final String? horaProgLlegada;
  final String? horaRealSalida;
  final String? horaRealLlegada;
  final String estado;

  const _Header({
    required this.embarcacionNombre,
    required this.responsableNombre,
    this.responsableTelefono,
    required this.operadorNombre,
    required this.fecha,
    required this.horaProgSalida,
    required this.horaProgLlegada,
    required this.horaRealSalida,
    required this.horaRealLlegada,
    required this.estado,
  });

  Future<void> _llamar() async {
    final telefono = responsableTelefono;
    if (telefono == null || telefono.isEmpty) return;
    final uri = Uri(scheme: 'tel', path: telefono);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri);
    }
  }

  Color get _color {
    switch (estado) {
      case 'abordando':
        return AppTheme.warning;
      case 'en_curso':
        return AppTheme.info;
      case 'finalizado':
        return AppTheme.success;
      default:
        return AppTheme.textMuted;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.primary,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.directions_boat_rounded,
                  color: Colors.white, size: 26),
              const SizedBox(width: 8),
              Expanded(
                child: Text(
                  embarcacionNombre,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 17,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: _color.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: _color),
                ),
                child: Text(
                  estado.toUpperCase(),
                  style: TextStyle(
                    color: _color,
                    fontSize: 10,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ],
          ),
          const Divider(color: Colors.white24, height: 24),
          _fila('Responsable', responsableNombre),
          if (responsableTelefono != null && responsableTelefono!.isNotEmpty)
            InkWell(
              onTap: _llamar,
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: 2),
                child: Row(
                  children: [
                    const SizedBox(
                      width: 130,
                      child: Text('Teléfono',
                          style: TextStyle(
                              color: Colors.white60, fontSize: 12)),
                    ),
                    Expanded(
                      child: Row(
                        children: [
                          Text(responsableTelefono!,
                              style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  decoration: TextDecoration.underline)),
                          const SizedBox(width: 6),
                          const Icon(Icons.call, color: Colors.white, size: 14),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            ),
          _fila('Operador', operadorNombre),
          _fila(
              'Fecha',
              fecha.isNotEmpty
                  ? DateFormat('EEEE, d MMMM yyyy', 'es')
                      .format(DateTime.parse(fecha))
                  : ''),
          _fila('Salida programada', horaProgSalida ?? '—'),
          _fila('Llegada programada', horaProgLlegada ?? '—'),
          if (horaRealSalida != null) _fila('Salida real', horaRealSalida!),
          if (horaRealLlegada != null) _fila('Llegada real', horaRealLlegada!),
        ],
      ),
    );
  }

  Widget _fila(String label, String value) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 2),
        child: Row(
          children: [
            SizedBox(
              width: 130,
              child: Text(label,
                  style: const TextStyle(color: Colors.white60, fontSize: 12)),
            ),
            Expanded(
              child: Text(value,
                  style: const TextStyle(
                      color: Colors.white,
                      fontSize: 12,
                      fontWeight: FontWeight.w600)),
            ),
          ],
        ),
      );
}

class _SectionCard extends StatelessWidget {
  final String title;
  final Widget child;
  final Widget? trailing;

  const _SectionCard({required this.title, required this.child, this.trailing});

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withOpacity(0.05),
              blurRadius: 8,
              offset: const Offset(0, 2)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 8, 8),
            child: Row(
              children: [
                Text(title,
                    style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: AppTheme.primary)),
                const Spacer(),
                if (trailing != null) trailing!,
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
            child: child,
          ),
        ],
      ),
    );
  }
}

class _ChecklistSection extends StatelessWidget {
  final String titulo;
  final String subtitulo;
  final List<dynamic> pasajeros;
  final bool soloLectura;
  final bool Function(Map<String, dynamic>) valorActual;
  final void Function(int id, bool value)? onCambiar;
  final TextEditingController Function(int id, String? initial)? obsController;
  final String obsKey;
  final String Function(int marcados, int total) contadorLabel;
  final VoidCallback? onGuardar;

  const _ChecklistSection({
    required this.titulo,
    required this.subtitulo,
    required this.pasajeros,
    required this.soloLectura,
    required this.valorActual,
    required this.onCambiar,
    required this.obsController,
    required this.obsKey,
    required this.contadorLabel,
    required this.onGuardar,
  });

  @override
  Widget build(BuildContext context) {
    final marcados =
        pasajeros.where((p) => valorActual(p as Map<String, dynamic>)).length;

    return _SectionCard(
      title: titulo,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(subtitulo,
              style: const TextStyle(fontSize: 12, color: AppTheme.textMuted)),
          const SizedBox(height: 8),
          if (pasajeros.isEmpty)
            const Text('No hay pasajeros para mostrar aquí.',
                style: TextStyle(fontSize: 12, color: AppTheme.textMuted))
          else
            ...pasajeros.map((p) {
              final m = p as Map<String, dynamic>;
              final id = m['id'] as int;
              return Column(
                children: [
                  CheckboxListTile(
                    value: valorActual(m),
                    onChanged: soloLectura
                        ? null
                        : (v) => onCambiar?.call(id, v ?? false),
                    controlAffinity: ListTileControlAffinity.leading,
                    activeColor: AppTheme.success,
                    contentPadding: EdgeInsets.zero,
                    title: Text(m['nombre'] ?? '',
                        style: const TextStyle(
                            fontWeight: FontWeight.w600, fontSize: 13)),
                    subtitle: Text(
                        'CI: ${m['cedula'] ?? ''}'
                        '${m['tipo'] != null ? ' · ${tipoLabel(m['tipo'] as String)}' : ''}'
                        '${(m['carrera'] as String?)?.isNotEmpty == true ? ' · ${m['carrera']}' : ''}',
                        style: const TextStyle(fontSize: 11)),
                  ),
                  if (!soloLectura && obsController != null)
                    Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: TextField(
                        controller: obsController!(id, m[obsKey] as String?),
                        decoration: const InputDecoration(
                          isDense: true,
                          hintText: 'Observaciones (opcional)',
                          border: OutlineInputBorder(),
                        ),
                        style: const TextStyle(fontSize: 12),
                      ),
                    )
                  else if (soloLectura &&
                      (m[obsKey] as String?)?.isNotEmpty == true)
                    Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: Text(
                        'Obs: ${m[obsKey]}',
                        style: const TextStyle(
                            fontSize: 11, color: AppTheme.textMuted),
                      ),
                    ),
                ],
              );
            }),
          const SizedBox(height: 4),
          Text(
            contadorLabel(marcados, pasajeros.length),
            style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 12),
          ),
          if (onGuardar != null) ...[
            const SizedBox(height: 10),
            ElevatedButton(
              onPressed: onGuardar,
              style: ElevatedButton.styleFrom(
                  minimumSize: const Size(double.infinity, 42)),
              child: const Text('Guardar checklist'),
            ),
          ],
        ],
      ),
    );
  }
}

class _EvidenciaSection extends StatelessWidget {
  final String titulo;
  final List<dynamic> fotos;
  final VoidCallback onAgregar;
  final void Function(int evidenciaId)? onEliminar;

  const _EvidenciaSection({
    required this.titulo,
    required this.fotos,
    required this.onAgregar,
    this.onEliminar,
  });

  @override
  Widget build(BuildContext context) {
    return _SectionCard(
      title: titulo,
      trailing: TextButton.icon(
        onPressed: onAgregar,
        icon: const Icon(Icons.add_a_photo_outlined, size: 18),
        label: const Text('Agregar fotos'),
      ),
      child: fotos.isEmpty
          ? const Text('Sin fotos todavía.',
              style: TextStyle(fontSize: 12, color: AppTheme.textMuted))
          : Wrap(
              spacing: 8,
              runSpacing: 8,
              children: fotos.map((f) {
                final m = f as Map;
                final url = m['url'] as String;
                final id = m['id'] as int?;
                return Stack(
                  clipBehavior: Clip.none,
                  children: [
                    ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: CachedNetworkImage(
                        imageUrl: url,
                        width: 80,
                        height: 80,
                        fit: BoxFit.cover,
                        memCacheWidth: 240,
                        memCacheHeight: 240,
                        placeholder: (_, __) => Container(
                          width: 80,
                          height: 80,
                          color: Colors.grey[200],
                        ),
                        errorWidget: (_, __, ___) => Container(
                          width: 80,
                          height: 80,
                          color: Colors.grey[200],
                          child: const Icon(Icons.broken_image_outlined),
                        ),
                      ),
                    ),
                    if (onEliminar != null && id != null)
                      Positioned(
                        top: -6,
                        right: -6,
                        child: GestureDetector(
                          onTap: () => onEliminar!(id),
                          child: Container(
                            padding: const EdgeInsets.all(3),
                            decoration: const BoxDecoration(
                              color: AppTheme.error,
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(Icons.close,
                                size: 14, color: Colors.white),
                          ),
                        ),
                      ),
                  ],
                );
              }).toList(),
            ),
    );
  }
}

// Formulario para agregar/editar un pasajero de último momento
class _PasajeroFormSheet extends StatefulWidget {
  final Map<String, dynamic>? existente;
  const _PasajeroFormSheet({this.existente});

  @override
  State<_PasajeroFormSheet> createState() => _PasajeroFormSheetState();
}

class _PasajeroFormSheetState extends State<_PasajeroFormSheet> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nombre;
  late final TextEditingController _cedula;
  late final TextEditingController _telefono;
  late final TextEditingController _email;
  late String _tipo;
  String? _facultad;
  String? _carrera;

  @override
  void initState() {
    super.initState();
    final e = widget.existente;
    final carreraExistente = e?['carrera'] as String?;
    final facultadExistente = e?['facultad'] as String?;
    _nombre = TextEditingController(text: e?['nombre'] ?? '');
    _cedula = TextEditingController(text: e?['cedula'] ?? '');
    _telefono = TextEditingController(text: e?['telefono'] ?? '');
    _email = TextEditingController(text: e?['email'] ?? '');
    _tipo = e?['tipo'] ?? 'externo';
    // Preferimos la facultad ya guardada; si no está (datos antiguos), la
    // inferimos a partir de la carrera usando el catálogo fijo.
    _facultad = (facultadExistente != null && facultadExistente.isNotEmpty)
        ? facultadExistente
        : (carreraExistente != null && carreraExistente.isNotEmpty
            ? facultadDeCarrera(carreraExistente)
            : null);
    _carrera = _facultad != null ? carreraExistente : null;
  }

  @override
  void dispose() {
    _nombre.dispose();
    _cedula.dispose();
    _telefono.dispose();
    _email.dispose();
    super.dispose();
  }

  void _guardar() {
    if (!_formKey.currentState!.validate()) return;
    Navigator.pop(context, {
      'nombre': _nombre.text.trim(),
      'cedula': _cedula.text.trim(),
      'tipo': _tipo,
      'carrera': _carrera ?? '',
      'facultad': _facultad ?? '',
      'telefono': _telefono.text.trim(),
      'email': _email.text.trim(),
    });
  }

  @override
  Widget build(BuildContext context) {
    final editando = widget.existente != null;
    return Padding(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
        left: 20,
        right: 20,
        top: 20,
      ),
      child: Form(
        key: _formKey,
        child: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 40,
                height: 4,
                margin: const EdgeInsets.only(bottom: 16),
                decoration: BoxDecoration(
                  color: Colors.grey[300],
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
              Text(
                editando ? 'Editar pasajero' : 'Agregar pasajero',
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w700,
                  color: AppTheme.primary,
                ),
              ),
              const SizedBox(height: 16),
              TextFormField(
                controller: _nombre,
                decoration: const InputDecoration(
                  labelText: 'Nombre completo',
                  prefixIcon: Icon(Icons.person_outline, size: 18),
                ),
                validator: (v) =>
                    v == null || v.trim().isEmpty ? 'Ingresa el nombre' : null,
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _cedula,
                keyboardType: TextInputType.number,
                maxLength: 10,
                decoration: const InputDecoration(
                  labelText: 'Cédula',
                  prefixIcon: Icon(Icons.badge_outlined, size: 18),
                  counterText: '',
                ),
                validator: (v) {
                  if (v == null || v.trim().isEmpty) return 'Ingresa la cédula';
                  if (v.trim().length != 10) return 'Debe tener 10 dígitos';
                  return null;
                },
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: _tipo,
                decoration: const InputDecoration(
                  labelText: 'Tipo',
                  prefixIcon: Icon(Icons.category_outlined, size: 18),
                ),
                items: const [
                  DropdownMenuItem(
                      value: 'estudiante', child: Text('Estudiante')),
                  DropdownMenuItem(value: 'docente', child: Text('Docente')),
                  DropdownMenuItem(
                      value: 'administrativo', child: Text('Administrativo')),
                  DropdownMenuItem(value: 'externo', child: Text('Externo')),
                ],
                onChanged: (v) => setState(() => _tipo = v!),
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: _facultad,
                decoration: InputDecoration(
                  labelText:
                      _tipo == 'estudiante' ? 'Facultad' : 'Facultad (opcional)',
                  prefixIcon:
                      const Icon(Icons.account_balance_outlined, size: 18),
                ),
                isExpanded: true,
                items: kFacultadesCarreras.keys
                    .map((f) => DropdownMenuItem(
                          value: f,
                          child:
                              Text(f, overflow: TextOverflow.ellipsis, maxLines: 1),
                        ))
                    .toList(),
                onChanged: (v) => setState(() {
                  _facultad = v;
                  _carrera = null;
                }),
                validator: (v) {
                  if (_tipo == 'estudiante' && (v == null || v.isEmpty)) {
                    return 'Selecciona la facultad';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                value: _carrera,
                decoration: InputDecoration(
                  labelText:
                      _tipo == 'estudiante' ? 'Carrera' : 'Carrera (opcional)',
                  prefixIcon: const Icon(Icons.school_outlined, size: 18),
                ),
                isExpanded: true,
                items: (_facultad != null
                        ? kFacultadesCarreras[_facultad]!
                        : const <String>[])
                    .map((c) => DropdownMenuItem(
                          value: c,
                          child:
                              Text(c, overflow: TextOverflow.ellipsis, maxLines: 1),
                        ))
                    .toList(),
                onChanged: _facultad == null
                    ? null
                    : (v) => setState(() => _carrera = v),
                validator: (v) {
                  if (_tipo == 'estudiante' && (v == null || v.isEmpty)) {
                    return 'Selecciona la carrera';
                  }
                  return null;
                },
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _telefono,
                keyboardType: TextInputType.phone,
                decoration: const InputDecoration(
                  labelText: 'Teléfono (opcional)',
                  prefixIcon: Icon(Icons.phone_outlined, size: 18),
                ),
              ),
              const SizedBox(height: 12),
              TextFormField(
                controller: _email,
                keyboardType: TextInputType.emailAddress,
                decoration: const InputDecoration(
                  labelText: 'Correo (opcional)',
                  prefixIcon: Icon(Icons.email_outlined, size: 18),
                ),
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: _guardar,
                style: ElevatedButton.styleFrom(
                  minimumSize: const Size(double.infinity, 48),
                ),
                child: Text(editando ? 'Guardar cambios' : 'Agregar'),
              ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
    );
  }
}
