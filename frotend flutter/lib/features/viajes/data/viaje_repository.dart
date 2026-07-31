import 'package:dio/dio.dart';
import 'package:image_picker/image_picker.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

class ViajeRepository {
  final _dio = ApiClient().dio;

  Future<Map<String, dynamic>> escanear(String codigo) async {
    final response = await _dio.post(ApiEndpoints.viajeEscanear(codigo));
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al escanear el código');
  }

  Future<Map<String, dynamic>> getViaje(int id) async {
    final response = await _dio.get(ApiEndpoints.viaje(id));
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al cargar el viaje');
  }

  /// [propio] = true fuerza ver solo los viajes escaneados por el usuario
  /// autenticado, aunque sea admin (usado por el diálogo de "continuar viaje"
  /// al abrir la app, que siempre debe ser personal). Sin el parámetro, un
  /// admin ve todos los viajes activos del sistema y un operador solo los suyos.
  Future<List<Map<String, dynamic>>> getActivos({bool propio = false}) async {
    final response = await _dio.get(
      ApiEndpoints.viajesActivos,
      queryParameters: propio ? {'propio': 1} : null,
    );
    if (response.statusCode == 200) {
      final data = response.data['data'] as List? ?? [];
      return data.cast<Map<String, dynamic>>();
    }
    throw Exception('Error al cargar viajes en curso');
  }

  Future<Map<String, dynamic>> checklistEmbarque(
    int id,
    List<Map<String, dynamic>> pasajeros,
  ) async {
    final response = await _dio.patch(
      ApiEndpoints.viajeChecklistEmbarque(id),
      data: {'pasajeros': pasajeros},
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al guardar el checklist de embarque');
  }

  Future<Map<String, dynamic>> checklistLlegada(
    int id,
    List<Map<String, dynamic>> pasajeros,
  ) async {
    final response = await _dio.patch(
      ApiEndpoints.viajeChecklistLlegada(id),
      data: {'pasajeros': pasajeros},
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al guardar el checklist de llegada');
  }

  Future<Map<String, dynamic>> subirEvidencia(
    int id,
    String tipo,
    List<XFile> fotos,
  ) async {
    final formData = FormData();
    formData.fields.add(MapEntry('tipo', tipo));
    for (final foto in fotos) {
      final bytes = await foto.readAsBytes();
      formData.files.add(MapEntry(
        'fotos[]',
        MultipartFile.fromBytes(bytes, filename: foto.name),
      ));
    }

    final response = await _dio.post(
      ApiEndpoints.viajeEvidencias(id),
      data: formData,
    );
    if (response.statusCode == 201) return response.data;
    throw Exception(response.data['error'] ?? 'Error al subir la evidencia');
  }

  Future<Map<String, dynamic>> eliminarEvidencia(
    int id,
    int evidenciaId,
  ) async {
    final response = await _dio.delete(
      ApiEndpoints.viajeEliminarEvidencia(id, evidenciaId),
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al eliminar la evidencia');
  }

  Future<Map<String, dynamic>> iniciar(int id) async {
    final response = await _dio.patch(ApiEndpoints.viajeIniciar(id));
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al confirmar la salida');
  }

  Future<Map<String, dynamic>> registrarLlegada(int id) async {
    final response = await _dio.patch(ApiEndpoints.viajeRegistrarLlegada(id));
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al registrar la llegada');
  }

  Future<Map<String, dynamic>> finalizar(int id, {String? observaciones}) async {
    final response = await _dio.patch(
      ApiEndpoints.viajeFinalizar(id),
      data: {'observaciones_cierre': observaciones},
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al finalizar el viaje');
  }

  Future<List<int>> descargarReportePdfBytes(int id) async {
    final response = await _dio.get(
      ApiEndpoints.viajeReportePdf(id),
      // Generar el PDF implica renderizar las fotos de evidencia del viaje
      // en el servidor — puede tardar más que el timeout global de 15s
      // usado por el resto de la app, sobre todo con varias fotos.
      options: Options(
        responseType:   ResponseType.bytes,
        receiveTimeout: const Duration(seconds: 45),
      ),
    );
    if (response.statusCode == 200) return response.data as List<int>;
    throw Exception('Error al descargar el reporte');
  }

  // ── Gestión de pasajeros (último momento) ─────────────────────────────

  Future<Map<String, dynamic>> agregarPasajero(
    int reservaId, {
    required String nombre,
    required String cedula,
    required String tipo,
    String? carrera,
    String? facultad,
    String? telefono,
    String? email,
  }) async {
    final response = await _dio.post(
      ApiEndpoints.reservaPasajeros(reservaId),
      data: {
        'nombre'  : nombre,
        'cedula'  : cedula,
        'tipo'    : tipo,
        'carrera' : carrera,
        'facultad': facultad,
        'telefono': telefono,
        'email'   : email,
      },
    );
    if (response.statusCode == 201) return response.data;
    throw Exception(response.data['error'] ?? 'Error al agregar pasajero');
  }

  Future<Map<String, dynamic>> editarPasajero(
    int reservaId,
    int pasajeroId, {
    required String nombre,
    required String cedula,
    required String tipo,
    String? carrera,
    String? facultad,
    String? telefono,
    String? email,
  }) async {
    final response = await _dio.put(
      ApiEndpoints.reservaPasajero(reservaId, pasajeroId),
      data: {
        'nombre'  : nombre,
        'cedula'  : cedula,
        'tipo'    : tipo,
        'carrera' : carrera,
        'facultad': facultad,
        'telefono': telefono,
        'email'   : email,
      },
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al editar pasajero');
  }

  Future<void> eliminarPasajero(int reservaId, int pasajeroId) async {
    final response = await _dio.delete(
      ApiEndpoints.reservaPasajero(reservaId, pasajeroId),
    );
    if (response.statusCode != 200) {
      throw Exception(response.data['error'] ?? 'Error al eliminar pasajero');
    }
  }
}
