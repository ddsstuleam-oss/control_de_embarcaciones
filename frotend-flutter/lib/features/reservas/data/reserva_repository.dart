import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

class ReservaRepository {
  final _dio = ApiClient().dio;

  Future<List<Map<String, dynamic>>> getMisReservas() async {
    final response = await _dio.get(ApiEndpoints.perfilReservas);
    if (response.statusCode == 200) {
      final raw = response.data;
      final List? data = raw is Map
          ? (raw['data'] ?? raw['reservas']) as List?
          : raw as List?;
      return (data ?? []).cast<Map<String, dynamic>>();
    }
    final msg = response.data is Map
        ? response.data['message'] ?? response.data['error']
        : null;
    throw Exception(msg ?? 'Error ${response.statusCode} al cargar reservas');
  }

  Future<Map<String, dynamic>> getReserva(int id) async {
    final response = await _dio.get(ApiEndpoints.reserva(id));
    if (response.statusCode == 200) {
      return response.data['data'];
    }
    throw Exception('Error al cargar reserva');
  }

  Future<Map<String, dynamic>> crearReserva({
    required int embarcacionId,
    required String fecha,
    required String horaInicio,
    required String horaFin,
    required int totalPersonas,
    required List<Map<String, dynamic>> pasajeros,

  }) async {
    final response = await _dio.post(
      ApiEndpoints.reservas,
      data: {
        'embarcacion_id' : embarcacionId,
        'fecha'          : fecha,
        'hora_inicio'    : horaInicio,
        'hora_fin'       : horaFin,
        'total_personas' : totalPersonas,
        'pasajeros'      : pasajeros,
      },
    );

    if (response.statusCode == 201) {
      return response.data;
    }

    final error = response.data['error'] ?? response.data['message'] ?? 'Error al crear reserva';
    throw Exception(error);
  }

  Future<void> cancelarReserva(int id) async {
    final response = await _dio.patch(ApiEndpoints.cancelarReserva(id));
    if (response.statusCode != 200) {
      throw Exception(response.data['error'] ?? 'Error al cancelar reserva');
    }
  }

  Future<List<Map<String, dynamic>>> getReservasPorFecha(String fecha) async {
    final response = await _dio.get(
      ApiEndpoints.reservasFecha,
      queryParameters: {'fecha': fecha},
    );
    if (response.statusCode == 200) {
      final List data = response.data['data'] ?? response.data;
      return data.cast<Map<String, dynamic>>();
    }
    throw Exception('Error al cargar reservas');
  }
}