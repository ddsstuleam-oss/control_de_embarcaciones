import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

class EmbarcacionRepository {
  final _dio = ApiClient().dio;

  Future<List<Map<String, dynamic>>> getEmbarcaciones() async {
    final response = await _dio.get(ApiEndpoints.embarcaciones);
    if (response.statusCode == 200) {
      final List data = response.data['data'] ?? response.data;
      return data.cast<Map<String, dynamic>>();
    }
    throw Exception('Error al cargar embarcaciones');
  }

  Future<List<Map<String, dynamic>>> getDisponibilidad(String fecha) async {
    final response = await _dio.get(
      ApiEndpoints.disponibilidad,
      queryParameters: {'fecha': fecha},
    );
    if (response.statusCode == 200) {
      final List data = response.data['embarcaciones'];
      return data.cast<Map<String, dynamic>>();
    }
    throw Exception('Error al cargar disponibilidad');
  }

  Future<Map<String, dynamic>> getEmbarcacion(int id) async {
    final response = await _dio.get(ApiEndpoints.embarcacion(id));
    if (response.statusCode == 200) {
      return response.data['data'];
    }
    throw Exception('Error al cargar embarcación');
  }
}