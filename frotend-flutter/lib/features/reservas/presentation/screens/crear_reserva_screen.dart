import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../providers/reserva_provider.dart';
import '../../../../core/api/api_client.dart';
import '../../../../core/api/api_endpoints.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../core/widgets/web_centered.dart';
import '../../../auth/providers/auth_provider.dart';
import '../../../embarcaciones/providers/embarcacion_provider.dart';

// ── Catálogo de facultades y carreras (ULEAM) ──────────────────────────────
const Map<String, List<String>> kFacultadesCarreras = {
  'Facultad Ciencias de la Salud': [
    'Medicina',
    'Odontología',
    'Enfermería',
    'Fisioterapia',
    'Fonoaudiología',
    'Laboratorio Clínico',
    'Terapia Ocupacional',
    'Psicología',
    'Nutrición y Dietética',
  ],
  'Facultad Ciencias Administrativas, Contables y Comercio': [
    'Administración de Empresas',
    'Mercadotecnia o Marketing',
    'Contabilidad y Auditoría',
    'Auditoría y Control de Gestión',
    'Finanzas',
    'Comercio Exterior',
    'Gestión de la Información Gerencial',
    'Gestión del Talento Humano',
  ],
  'Facultad de Educación y Turismo': [
    'Educación Inicial',
    'Educación Especial',
    'Psicología Educativa',
    'Educación Básica',
    'Pedagogía de la Actividad Física y el Deporte',
    'Pedagogía de la Lengua y la Literatura',
    'Pedagogía de los Idiomas Nacionales y Extranjeros',
    'Turismo',
    'Hospitalidad y Hotelería',
    'Artes Plásticas',
    'Sociología',
    'Artes Escénicas',
    'Educación Básica Bilingüe',
    'Arqueología',
    'Diseño Textil e Indumentaria',
    'Educación Inicial bilingüe',
    'Entrenamiento Deportivo',
    'Gestión Hotelera Internacional',
    'Educación Inclusiva',
  ],
  'Facultad Ingeniería, Industria y Arquitectura': [
    'Ingeniería Civil',
    'Ingeniería Marítima',
    'Electricidad',
    'Arquitectura',
    'Ingeniería Industrial',
  ],
  'Facultad Ciencias de la Vida y Tecnologías': [
    'Ingeniería Agropecuaria',
    'Agronegocios',
    'Ingeniería Agroindustrial',
    'Ingeniería Ambiental',
    'Ingeniería en Tecnologías de la Información',
    'Ingeniería en Software',
    'Ingeniería en Sistema',
    'Biología',
    'Ingeniería de Alimentos',
  ],
  'Facultad de Ciencias Sociales, Derecho y Bienestar': [
    'Derecho',
    'Criminología y Ciencias Forenses',
    'Economía',
    'Trabajo Social',
    'Comunicación',
    'Gestión Pública y Desarrollo',
  ],
  'Facultad de Artes, Humanidades y Patrimonio': [
    'Artes Pláticas',
    'Arqueología',
    'Artes escénicas',
    'Diseño Textil e Indumentaria',
    'Sociología',
  ],
};

/// Busca la facultad a la que pertenece [carrera]. Devuelve null si no
/// coincide con ninguna carrera del catálogo (p. ej. datos antiguos en
/// texto libre que ya no calzan con la lista fija).
String? facultadDeCarrera(String carrera) {
  for (final entry in kFacultadesCarreras.entries) {
    if (entry.value.contains(carrera)) return entry.key;
  }
  return null;
}

String tipoLabel(String tipo) {
  switch (tipo) {
    case 'estudiante':
      return 'Estudiante';
    case 'docente':
      return 'Docente';
    case 'administrativo':
      return 'Administrativo';
    default:
      return 'Externo';
  }
}

class CrearReservaScreen extends ConsumerStatefulWidget {
  final int? embarcacionId;

  const CrearReservaScreen({super.key, this.embarcacionId});

  @override
  ConsumerState<CrearReservaScreen> createState() => _CrearReservaScreenState();
}

class _CrearReservaScreenState extends ConsumerState<CrearReservaScreen> {
  final _formKey = GlobalKey<FormState>();
  DateTime _fecha = DateTime.now().add(const Duration(days: 1));
  bool _isLoading = false;

  // Horario
  TimeOfDay? _horaInicio;
  TimeOfDay? _horaFin;
  List<Map<String, dynamic>> _horariosOcupados = [];
  bool _cargandoHorarios = false;

  final List<TimeOfDay> _slots =
      List.generate(24, (i) => TimeOfDay(hour: i, minute: 0)); // 00:00–23:00

  // Lista de pasajeros
  final List<Map<String, TextEditingController>> _pasajeros = [];
  final List<String> _tipos = [];
  final List<GlobalKey<_PasajeroFormState>> _pasajeroKeys = [];

  @override
  void initState() {
    super.initState();
    _agregarPasajero();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _cargarHorariosOcupados();
      _verificarTelefono();
    });
  }

  /// El teléfono de contacto es obligatorio para reservar (permite ubicar
  /// al titular en caso de emergencia). Si la cuenta no lo tiene guardado,
  /// se manda a completarlo antes de dejar seguir con el formulario.
  void _verificarTelefono() {
    final telefono = ref.read(authProvider).user?['telefono'] as String?;
    if (telefono == null || telefono.isEmpty) {
      context.push('/completar-telefono');
    }
  }

  @override
  void dispose() {
    for (final p in _pasajeros) {
      for (var c in p.values) {
        c.dispose();
      }
    }
    super.dispose();
  }

  // ── Pasajeros ────────────────────────────────────────────────────────────

  void _agregarPasajero() {
    // Minimiza las tarjetas anteriores que ya estén completas, para dejar
    // el foco en el pasajero nuevo.
    for (final key in _pasajeroKeys) {
      key.currentState?.colapsarSiCompleto();
    }
    setState(() {
      _pasajeros.add({
        'nombre': TextEditingController(),
        'cedula': TextEditingController(),
        'carrera': TextEditingController(),
        'facultad': TextEditingController(),
      });
      _tipos.add('externo');
      _pasajeroKeys.add(GlobalKey<_PasajeroFormState>());
    });
  }

  void _eliminarPasajero(int index) {
    if (_pasajeros.length <= 1) return;
    setState(() {
      for (var c in _pasajeros[index].values) {
        c.dispose();
      }
      _pasajeros.removeAt(index);
      _tipos.removeAt(index);
      _pasajeroKeys.removeAt(index);
    });
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    while (_tipos.length < _pasajeros.length) {
      _tipos.add('externo');
    }
  }

  // ── Fecha ────────────────────────────────────────────────────────────────

  Future<void> _seleccionarFecha() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: _fecha,
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
      setState(() {
        _fecha = picked;
        _horaInicio = null;
        _horaFin = null;
      });
      await _cargarHorariosOcupados();
    }
  }

  // ── Horarios ─────────────────────────────────────────────────────────────

  String _fmt(TimeOfDay t) =>
      '${t.hour.toString().padLeft(2, '0')}:${t.minute.toString().padLeft(2, '0')}';

  int _toMin(TimeOfDay t) => t.hour * 60 + t.minute;

  TimeOfDay _parseTime(String s) {
    final p = s.split(':');
    return TimeOfDay(hour: int.parse(p[0]), minute: int.parse(p[1]));
  }

  bool _slotOcupado(TimeOfDay slot) {
    final slotMin = _toMin(slot);
    final slotFinMin = slotMin + 60; // Cada slot es de 1 hora
    for (final h in _horariosOcupados) {
      final ocIni = _toMin(_parseTime(h['hora_inicio'] as String));
      final ocFin = _toMin(_parseTime(h['hora_fin'] as String));
      if (slotMin < ocFin && slotFinMin > ocIni) return true;
    }
    return false;
  }

  bool _esFechaHoy() {
    final now = DateTime.now();
    return _fecha.year == now.year &&
        _fecha.month == now.month &&
        _fecha.day == now.day;
  }

  // Deshabilita slots dentro de las próximas 2 horas si la fecha es hoy
  bool _slotPasado(TimeOfDay slot) {
    if (!_esFechaHoy()) return false;
    final now = DateTime.now();
    final limiteMin = now.hour * 60 + now.minute + 120; // +2 horas
    return _toMin(slot) < limiteMin;
  }

  bool _rangoContieneOcupado(TimeOfDay ini, TimeOfDay fin) {
    for (final slot in _slots) {
      if (_toMin(slot) >= _toMin(ini) && _toMin(slot) < _toMin(fin)) {
        if (_slotOcupado(slot)) return true;
      }
    }
    return false;
  }

  Future<void> _cargarHorariosOcupados() async {
    if (widget.embarcacionId == null) return;
    setState(() => _cargandoHorarios = true);
    try {
      final response = await ApiClient().dio.get(
        ApiEndpoints.horariosOcupados(widget.embarcacionId!),
        queryParameters: {
          'fecha': DateFormat('yyyy-MM-dd').format(_fecha),
        },
      );
      setState(() {
        _horariosOcupados = (response.data['horarios_ocupados'] as List)
            .cast<Map<String, dynamic>>();
      });
    } catch (_) {
    } finally {
      if (mounted) setState(() => _cargandoHorarios = false);
    }
  }

  void _seleccionarSlot(TimeOfDay slot) {
    if (_slotOcupado(slot) || _slotPasado(slot)) return;

    setState(() {
      if (_horaInicio != null &&
          _horaFin == null &&
          _toMin(slot) == _toMin(_horaInicio!)) {
        // Toca de nuevo la hora de inicio → desmarcar
        _horaInicio = null;
      } else if (_horaInicio == null ||
          (_horaInicio != null && _horaFin != null)) {
        // Primera selección → inicio
        _horaInicio = slot;
        _horaFin = null;
      } else {
        // Segunda selección → fin (se usa la hora tocada tal cual, sin sumarle nada)
        final finMin = _toMin(slot);
        final inicioMin = _toMin(_horaInicio!);

        if (finMin <= inicioMin) {
          // Si selecciona antes o igual → nuevo inicio
          _horaInicio = slot;
          _horaFin = null;
        } else {
          if (_rangoContieneOcupado(_horaInicio!, slot)) {
            ScaffoldMessenger.of(context).showSnackBar(
              const SnackBar(
                content: Text(
                    'El rango incluye horarios ya reservados. Elige otro.'),
                backgroundColor: AppTheme.error,
              ),
            );
          } else {
            _horaFin = slot;
          }
        }
      }
    });
  }

  // ── Crear reserva ─────────────────────────────────────────────────────────

  Future<void> _crearReserva() async {
    if (!_formKey.currentState!.validate()) return;

    if (_horaInicio == null || _horaFin == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Selecciona el horario del viaje'),
          backgroundColor: AppTheme.error,
        ),
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      final pasajerosData = _pasajeros.asMap().entries.map((e) {
        final i = e.key;
        final p = e.value;
        return {
          'nombre': p['nombre']!.text.trim(),
          'cedula': p['cedula']!.text.trim(),
          'tipo': i < _tipos.length ? _tipos[i] : 'externo',
          'carrera': p['carrera']!.text.trim().isEmpty
              ? null
              : p['carrera']!.text.trim(),
          'facultad': p['facultad']!.text.trim().isEmpty
              ? null
              : p['facultad']!.text.trim(),
        };
      }).toList();

      final result = await ref.read(reservaRepositoryProvider).crearReserva(
            embarcacionId: widget.embarcacionId!,
            fecha: DateFormat('yyyy-MM-dd').format(_fecha),
            horaInicio: _fmt(_horaInicio!),
            horaFin: _fmt(_horaFin!),
            totalPersonas: _pasajeros.length,
            pasajeros: pasajerosData,
          );

      if (!mounted) return;

      ref.invalidate(misReservasProvider);

      final reservaId = result['reserva']['id'];
      context.go('/reservas/$reservaId');

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Solicitud enviada. Un administrador debe aprobarla antes de confirmarse.',
          ),
          backgroundColor: AppTheme.warning,
        ),
      );
    } catch (e) {
      if (!mounted) return;
      final mensaje = e.toString().replaceAll('Exception: ', '');
      // Defensa adicional: si el backend rechazó por falta de teléfono
      // (p.ej. el dato en caché quedó desactualizado), manda a completarlo
      // en vez de solo mostrar el error.
      if (mensaje.contains('teléfono')) {
        context.push('/completar-telefono');
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(mensaje),
            backgroundColor: AppTheme.error,
          ),
        );
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  // ── Build ─────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    final embarcacionAsync = widget.embarcacionId != null
        ? ref.watch(embarcacionProvider(widget.embarcacionId!))
        : null;

    return Scaffold(
      backgroundColor: AppTheme.background,
      appBar: AppBar(title: const Text('Nueva reserva')),
      body: WebCentered(
        maxWidth: 560,
        child: Form(
          key: _formKey,
          child: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              // Info embarcación
              if (embarcacionAsync != null)
                embarcacionAsync.when(
                  loading: () => const SizedBox.shrink(),
                  error: (_, __) => const SizedBox.shrink(),
                  data: (e) => _InfoCard(
                    nombre: e['nombre'] ?? '',
                    capacidad: (e['capacidad'] as num?)?.toInt() ?? 0,
                  ),
                ),

              const SizedBox(height: 16),

              // ── Fecha ──────────────────────────────────────────
              _SectionCard(
                title: 'Fecha del viaje',
                child: InkWell(
                  onTap: _seleccionarFecha,
                  borderRadius: BorderRadius.circular(10),
                  child: Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.grey[100],
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(color: Colors.grey[300]!),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.calendar_today,
                            color: AppTheme.primary),
                        const SizedBox(width: 12),
                        Text(
                          DateFormat('EEEE, d MMMM yyyy', 'es').format(_fecha),
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                        const Spacer(),
                        const Icon(Icons.chevron_right,
                            color: AppTheme.textMuted),
                      ],
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 16),

              // ── Horario ────────────────────────────────────────
              _SectionCard(
                title: 'Horario del viaje',
                child: _cargandoHorarios
                    ? const Padding(
                        padding: EdgeInsets.symmetric(vertical: 20),
                        child: Center(
                          child: CircularProgressIndicator(
                              color: AppTheme.primary),
                        ),
                      )
                    : Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Info
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: AppTheme.info.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: const Row(
                              children: [
                                Icon(Icons.info_outline,
                                    color: AppTheme.info, size: 16),
                                SizedBox(width: 8),
                                Expanded(
                                  child: Text(
                                    'Toca la hora de inicio y luego la de fin',
                                    style: TextStyle(
                                      fontSize: 12,
                                      color: AppTheme.info,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 12),

                          // Grilla de slots
                          Wrap(
                            spacing: 8,
                            runSpacing: 8,
                            children: _slots.map((slot) {
                              final ocupado = _slotOcupado(slot);
                              final pasado = _slotPasado(slot);
                              final disabled = ocupado || pasado;
                              final slotMin = _toMin(slot);
                              final iniMin = _horaInicio != null
                                  ? _toMin(_horaInicio!)
                                  : -1;
                              final finMin =
                                  _horaFin != null ? _toMin(_horaFin!) : -1;
                              // Todo el rango seleccionado (inicio y fin
                              // incluidos) se resalta igual, para que coincida
                              // con el texto "Horario: HH:mm — HH:mm".
                              final seleccionado = _horaInicio != null &&
                                  slotMin >= iniMin &&
                                  (_horaFin == null
                                      ? slotMin == iniMin
                                      : slotMin <= finMin);

                              Color bgColor;
                              Color borderColor;
                              Color textColor;

                              if (ocupado) {
                                bgColor = Colors.red[50]!;
                                borderColor = Colors.red[200]!;
                                textColor = Colors.red[300]!;
                              } else if (pasado) {
                                bgColor = Colors.blueGrey[50]!;
                                borderColor = Colors.blueGrey[200]!;
                                textColor = Colors.blueGrey[300]!;
                              } else if (seleccionado) {
                                bgColor = AppTheme.primary;
                                borderColor = AppTheme.primary;
                                textColor = Colors.white;
                              } else {
                                bgColor = Colors.grey[100]!;
                                borderColor = Colors.grey[300]!;
                                textColor = AppTheme.textMuted;
                              }

                              return GestureDetector(
                                onTap: disabled
                                    ? null
                                    : () => _seleccionarSlot(slot),
                                child: Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 12,
                                    vertical: 8,
                                  ),
                                  decoration: BoxDecoration(
                                    color: bgColor,
                                    borderRadius: BorderRadius.circular(8),
                                    border: Border.all(color: borderColor),
                                  ),
                                  child: Text(
                                    _fmt(slot),
                                    style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.w600,
                                      color: textColor,
                                      decoration: pasado
                                          ? TextDecoration.lineThrough
                                          : TextDecoration.none,
                                      decorationColor: textColor,
                                    ),
                                  ),
                                ),
                              );
                            }).toList(),
                          ),

                          const SizedBox(height: 12),

                          // Horario seleccionado
                          if (_horaInicio != null)
                            Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                color: (_horaFin != null
                                        ? AppTheme.success
                                        : AppTheme.warning)
                                    .withOpacity(0.1),
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                  color: (_horaFin != null
                                          ? AppTheme.success
                                          : AppTheme.warning)
                                      .withOpacity(0.4),
                                ),
                              ),
                              child: Row(
                                children: [
                                  Icon(
                                    _horaFin != null
                                        ? Icons.check_circle_outline
                                        : Icons.access_time,
                                    color: _horaFin != null
                                        ? AppTheme.success
                                        : AppTheme.warning,
                                    size: 18,
                                  ),
                                  const SizedBox(width: 8),
                                  Text(
                                    _horaFin != null
                                        ? 'Horario: ${_fmt(_horaInicio!)} — ${_fmt(_horaFin!)}'
                                        : 'Selecciona la hora de fin',
                                    style: TextStyle(
                                      fontSize: 13,
                                      fontWeight: FontWeight.w600,
                                      color: _horaFin != null
                                          ? AppTheme.success
                                          : AppTheme.warning,
                                    ),
                                  ),
                                  if (_horaFin != null) ...[
                                    const Spacer(),
                                    GestureDetector(
                                      onTap: () => setState(() {
                                        _horaInicio = null;
                                        _horaFin = null;
                                      }),
                                      child: const Icon(Icons.close,
                                          size: 16,
                                          color: Color.fromARGB(
                                              255, 136, 136, 136)),
                                    ),
                                  ],
                                ],
                              ),
                            ),

                          const SizedBox(height: 12),

                          // Leyenda
                          Wrap(
                            spacing: 12,
                            runSpacing: 6,
                            children: [
                              const _Leyenda(
                                  color: AppTheme.primary,
                                  label: 'Seleccionado'),
                              _Leyenda(
                                  color: Colors.red[300]!, label: 'Ocupado'),
                              const _Leyenda(
                                  color: Color.fromARGB(255, 189, 189, 189),
                                  label: 'Disponible'),
                              _Leyenda(
                                  color: Colors.blueGrey[300]!,
                                  label: 'No disponible'),
                            ],
                          ),
                        ],
                      ),
              ),

              const SizedBox(height: 16),

              // ── Pasajeros ──────────────────────────────────────
              _SectionCard(
                title: 'Pasajeros (${_pasajeros.length})',
                trailing: TextButton.icon(
                  onPressed: _agregarPasajero,
                  icon: const Icon(Icons.add, size: 18),
                  label: const Text('Agregar'),
                ),
                child: Column(
                  children: _pasajeros.asMap().entries.map((e) {
                    final i = e.key;
                    final p = e.value;
                    while (_tipos.length <= i) {
                      _tipos.add('externo');
                    }

                    while (_pasajeroKeys.length <= i) {
                      _pasajeroKeys.add(GlobalKey<_PasajeroFormState>());
                    }

                    return _PasajeroForm(
                      key: _pasajeroKeys[i],
                      index: i,
                      controllers: p,
                      tipo: _tipos[i],
                      onTipoChanged: (t) => setState(() => _tipos[i] = t),
                      onRemove: _pasajeros.length > 1
                          ? () => _eliminarPasajero(i)
                          : null,
                    );
                  }).toList(),
                ),
              ),

              const SizedBox(height: 24),

              // ── Botón confirmar ────────────────────────────────
              ElevatedButton(
                onPressed: _isLoading ? null : _crearReserva,
                child: _isLoading
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(
                          color: Colors.white,
                          strokeWidth: 2,
                        ),
                      )
                    : Text(
                        'Enviar solicitud (${_pasajeros.length} '
                        'pasajero${_pasajeros.length > 1 ? 's' : ''})',
                      ),
              ),

              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }
}

// ── Widgets ───────────────────────────────────────────────────────────────────

class _Leyenda extends StatelessWidget {
  final Color color;
  final String label;
  const _Leyenda({required this.color, required this.label});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(
            color: color,
            borderRadius: BorderRadius.circular(3),
          ),
        ),
        const SizedBox(width: 4),
        Text(
          label,
          style: const TextStyle(
            fontSize: 10,
            color: AppTheme.textMuted,
          ),
        ),
      ],
    );
  }
}

class _InfoCard extends StatelessWidget {
  final String nombre;
  final int capacidad;

  const _InfoCard({required this.nombre, required this.capacidad});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppTheme.primary,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          const Icon(Icons.directions_boat_rounded,
              color: Colors.white, size: 32),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  nombre,
                  overflow: TextOverflow.ellipsis,
                  maxLines: 1,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                Text(
                  'Capacidad: $capacidad pasajeros',
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    color: Colors.white70,
                    fontSize: 12,
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

class _SectionCard extends StatelessWidget {
  final String title;
  final Widget child;
  final Widget? trailing;

  const _SectionCard({
    required this.title,
    required this.child,
    this.trailing,
  });

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
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 8, 8),
            child: Row(
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w700,
                    color: AppTheme.primary,
                  ),
                ),
                const Spacer(),
                if (trailing != null) trailing!,
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
            child: child,
          ),
        ],
      ),
    );
  }
}

class _PasajeroForm extends StatefulWidget {
  final int index;
  final Map<String, TextEditingController> controllers;
  final String tipo;
  final void Function(String) onTipoChanged;
  final VoidCallback? onRemove;

  const _PasajeroForm({
    super.key,
    required this.index,
    required this.controllers,
    required this.tipo,
    required this.onTipoChanged,
    this.onRemove,
  });

  @override
  State<_PasajeroForm> createState() => _PasajeroFormState();
}

class _PasajeroFormState extends State<_PasajeroForm> {
  List<Map<String, dynamic>> _sugerencias = [];
  bool _buscando = false;
  bool _personaSeleccionada = false;
  String? _facultad;
  String? _carrera;
  bool _expanded = true;

  bool _datosCompletos() {
    final nombre = widget.controllers['nombre']!.text.trim();
    final cedula = widget.controllers['cedula']!.text.trim();
    final requiereCarrera = widget.tipo == 'estudiante';
    final carreraOk = !requiereCarrera || (_carrera != null && _carrera!.isNotEmpty);
    return nombre.isNotEmpty && cedula.length == 10 && carreraOk;
  }

  void _toggleExpand() {
    if (_expanded && !_datosCompletos()) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Completa los datos del pasajero antes de minimizarlo'),
          backgroundColor: AppTheme.warning,
        ),
      );
      return;
    }
    setState(() => _expanded = !_expanded);
  }

  /// Minimiza esta tarjeta si ya tiene los datos básicos completos.
  /// Se usa cuando se agrega un nuevo pasajero, para no interrumpir con un
  /// aviso al usuario si esta tarjeta aún está incompleta.
  void colapsarSiCompleto() {
    if (_expanded && _datosCompletos()) {
      setState(() => _expanded = false);
    }
  }

  Future<void> _buscarPersona(String query) async {
    if (query.length < 2 || _personaSeleccionada) {
      setState(() => _sugerencias = []);
      return;
    }

    setState(() => _buscando = true);

    try {
      final response = await ApiClient().dio.get(
        ApiEndpoints.directorioBuscar,
        queryParameters: {'q': query},
      );
      if (mounted) {
        setState(() {
          _sugerencias =
              (response.data['personas'] as List).cast<Map<String, dynamic>>();
        });
      }
    } catch (_) {
    } finally {
      if (mounted) setState(() => _buscando = false);
    }
  }

  void _seleccionarPersona(Map<String, dynamic> persona) {
    final carreraPersona = persona['carrera'] as String?;
    final facultadPersona = persona['facultad'] as String?;
    widget.controllers['nombre']!.text = persona['nombre'] ?? '';
    widget.controllers['cedula']!.text = persona['cedula'] ?? '';
    widget.controllers['carrera']!.text = carreraPersona ?? '';
    widget.onTipoChanged(persona['tipo'] ?? 'externo');
    setState(() {
      _sugerencias = [];
      _personaSeleccionada = true;
      // Preferimos la facultad ya guardada en el directorio; si no está
      // (registros antiguos importados sin ese dato), la inferimos a
      // partir de la carrera usando el catálogo fijo.
      _facultad = facultadPersona != null && facultadPersona.isNotEmpty
          ? facultadPersona
          : (carreraPersona != null && carreraPersona.isNotEmpty
              ? facultadDeCarrera(carreraPersona)
              : null);
      _carrera = _facultad != null ? carreraPersona : null;
      widget.controllers['facultad']!.text = _facultad ?? '';
      // Datos ya validados por el directorio: se muestra minimizado.
      _expanded = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.only(bottom: _expanded ? 16 : 8),
      padding: _expanded
          ? const EdgeInsets.all(12)
          : const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.grey[50],
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: Colors.grey[200]!),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: AppTheme.primary,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  'Pasajero ${widget.index + 1}',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              const Spacer(),
              if (_personaSeleccionada)
                GestureDetector(
                  onTap: () {
                    setState(() {
                      _personaSeleccionada = false;
                      _facultad = null;
                      _carrera = null;
                      _expanded = true;
                    });
                    widget.controllers['nombre']!.clear();
                    widget.controllers['cedula']!.clear();
                    widget.controllers['carrera']!.clear();
                    widget.controllers['facultad']!.clear();
                  },
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: AppTheme.info.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.check_circle,
                            size: 12, color: AppTheme.info),
                        SizedBox(width: 4),
                        Text('Del directorio',
                            style: TextStyle(
                              fontSize: 10,
                              color: AppTheme.info,
                              fontWeight: FontWeight.w600,
                            )),
                      ],
                    ),
                  ),
                ),
              IconButton(
                icon: Icon(
                  _expanded ? Icons.unfold_less : Icons.unfold_more,
                  size: 18,
                  color: AppTheme.textMuted,
                ),
                tooltip: _expanded ? 'Minimizar' : 'Ver más',
                onPressed: _toggleExpand,
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
              if (widget.onRemove != null) ...[
                const SizedBox(width: 4),
                IconButton(
                  icon:
                      const Icon(Icons.close, size: 18, color: AppTheme.error),
                  onPressed: widget.onRemove,
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
              ],
            ],
          ),

          if (!_expanded) ...[
            const SizedBox(height: 6),
            _buildResumen(),
          ] else ...[
          const SizedBox(height: 10),

          // ── Buscador ─────────────────────────────────────
          TextFormField(
            controller: widget.controllers['nombre'],
            onChanged: (v) {
              _personaSeleccionada = false;
              _buscarPersona(v);
            },
            decoration: InputDecoration(
              labelText: 'Nombre completo',
              prefixIcon: const Icon(Icons.person_outline, size: 18),
              suffixIcon: _buscando
                  ? const Padding(
                      padding: EdgeInsets.all(12),
                      child: SizedBox(
                        width: 16,
                        height: 16,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: AppTheme.primary,
                        ),
                      ),
                    )
                  : const Icon(Icons.search, size: 18),
              isDense: true,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
            validator: (v) =>
                v == null || v.isEmpty ? 'Ingresa el nombre' : null,
          ),

          // ── Sugerencias ──────────────────────────────────
          if (_sugerencias.isNotEmpty)
            Container(
              margin: const EdgeInsets.only(top: 4),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.grey[300]!),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.08),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                children: _sugerencias
                    .map((p) => InkWell(
                          onTap: () => _seleccionarPersona(p),
                          child: Padding(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 12, vertical: 10),
                            child: Row(
                              children: [
                                Container(
                                  width: 28,
                                  height: 28,
                                  decoration: BoxDecoration(
                                    color: AppTheme.primary.withOpacity(0.1),
                                    shape: BoxShape.circle,
                                  ),
                                  child: Center(
                                    child: Text(
                                      (p['nombre'] as String? ?? 'P')[0]
                                          .toUpperCase(),
                                      style: const TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.w700,
                                        color: AppTheme.primary,
                                      ),
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 10),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        p['nombre'] ?? '',
                                        style: const TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                                      Text(
                                        'CI: ${p['cedula']} · ${(p['tipo'] ?? '').toString().toUpperCase()}',
                                        style: const TextStyle(
                                          fontSize: 11,
                                          color: AppTheme.textMuted,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const Icon(Icons.add_circle_outline,
                                    size: 18, color: AppTheme.success),
                              ],
                            ),
                          ),
                        ))
                    .toList(),
              ),
            ),

          const SizedBox(height: 10),

          // Cédula
          TextFormField(
            controller: widget.controllers['cedula'],
            keyboardType: TextInputType.number,
            maxLength: 10,
            onChanged: (v) {
              if (!_personaSeleccionada && v.length >= 3) {
                _buscarPersona(v);
              }
            },
            decoration: const InputDecoration(
              labelText: 'Cédula',
              prefixIcon: Icon(Icons.badge_outlined, size: 18),
              isDense: true,
              counterText: '',
              contentPadding:
                  EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
            validator: (v) {
              if (v == null || v.isEmpty) return 'Ingresa la cédula';
              if (v.length != 10) return 'Debe tener 10 dígitos';
              return null;
            },
          ),
          const SizedBox(height: 10),

          // Tipo
          DropdownButtonFormField<String>(
            value: widget.tipo,
            decoration: const InputDecoration(
              labelText: 'Tipo',
              prefixIcon: Icon(Icons.category_outlined, size: 18),
              isDense: true,
              contentPadding:
                  EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
            items: const [
              DropdownMenuItem(value: 'estudiante', child: Text('Estudiante')),
              DropdownMenuItem(value: 'docente', child: Text('Docente')),
              DropdownMenuItem(
                  value: 'administrativo', child: Text('Administrativo')),
              DropdownMenuItem(value: 'externo', child: Text('Externo')),
            ],
            onChanged: (v) => widget.onTipoChanged(v!),
          ),
          const SizedBox(height: 10),

          // Facultad
          DropdownButtonFormField<String>(
            value: _facultad,
            decoration: InputDecoration(
              labelText: widget.tipo == 'estudiante'
                  ? 'Facultad'
                  : 'Facultad (opcional)',
              prefixIcon: const Icon(Icons.account_balance_outlined, size: 18),
              isDense: true,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
            isExpanded: true,
            items: kFacultadesCarreras.keys
                .map((f) => DropdownMenuItem(
                      value: f,
                      child: Text(f,
                          overflow: TextOverflow.ellipsis, maxLines: 1),
                    ))
                .toList(),
            onChanged: (v) => setState(() {
              _facultad = v;
              _carrera = null;
              widget.controllers['carrera']!.clear();
              widget.controllers['facultad']!.text = v ?? '';
            }),
            validator: (v) {
              if (widget.tipo == 'estudiante' && (v == null || v.isEmpty)) {
                return 'Selecciona la facultad';
              }
              return null;
            },
          ),
          const SizedBox(height: 10),

          // Carrera (depende de la facultad elegida)
          DropdownButtonFormField<String>(
            value: _carrera,
            decoration: InputDecoration(
              labelText:
                  widget.tipo == 'estudiante' ? 'Carrera' : 'Carrera (opcional)',
              prefixIcon: const Icon(Icons.school_outlined, size: 18),
              isDense: true,
              contentPadding:
                  const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
            ),
            isExpanded: true,
            items: (_facultad != null ? kFacultadesCarreras[_facultad]! : const <String>[])
                .map((c) => DropdownMenuItem(
                      value: c,
                      child: Text(c,
                          overflow: TextOverflow.ellipsis, maxLines: 1),
                    ))
                .toList(),
            onChanged: _facultad == null
                ? null
                : (v) => setState(() {
                      _carrera = v;
                      widget.controllers['carrera']!.text = v ?? '';
                    }),
            validator: (v) {
              if (widget.tipo == 'estudiante' && (v == null || v.isEmpty)) {
                return 'Selecciona la carrera';
              }
              return null;
            },
          ),
          ],
        ],
      ),
    );
  }

  Widget _buildResumen() {
    final nombre = widget.controllers['nombre']!.text.trim();
    final cedula = widget.controllers['cedula']!.text.trim();
    final subtitulo = [
      if (cedula.isNotEmpty) 'CI: $cedula',
      tipoLabel(widget.tipo),
      if (_carrera != null) _carrera!,
    ].join(' · ');

    return InkWell(
      onTap: _toggleExpand,
      borderRadius: BorderRadius.circular(8),
      child: Row(
        children: [
          CircleAvatar(
            radius: 13,
            backgroundColor: AppTheme.primary.withOpacity(0.1),
            child: Text(
              nombre.isNotEmpty ? nombre[0].toUpperCase() : '?',
              style: const TextStyle(
                color: AppTheme.primary,
                fontWeight: FontWeight.w700,
                fontSize: 12,
              ),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  nombre.isEmpty ? 'Sin nombre' : nombre,
                  style: const TextStyle(
                    fontWeight: FontWeight.w600,
                    fontSize: 13,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  subtitulo,
                  style: const TextStyle(
                    fontSize: 11,
                    color: AppTheme.textMuted,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
