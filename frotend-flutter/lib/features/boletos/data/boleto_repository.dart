import 'package:dio/dio.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

class BoletoRepository {
  final _dio = ApiClient().dio;

Future<Map<String, dynamic>> getBoleto(int id) async {
  final response = await _dio.get(ApiEndpoints.boleto(id));
  if (response.statusCode == 200) {
    // El BoletoResource devuelve data directamente
    return response.data['data'] ?? response.data;
  }
  throw Exception('Error al cargar boleto');
}

  Future<Map<String, dynamic>> validarBoleto(String codigo) async {
    final response = await _dio.post(ApiEndpoints.validarBoleto(codigo));
    if (response.statusCode == 200) {
      return response.data;
    }
    throw Exception(response.data['error'] ?? 'Error al validar boleto');
  }

  Future<Map<String, dynamic>> embarcarBoleto(
      int boletoId, List<int> ausentes) async {
    final response = await _dio.patch(
      ApiEndpoints.embarcarBoleto(boletoId),
      data: {'pasajeros_ausentes': ausentes},
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al registrar embarque');
  }

  Future<String> getPdfUrl(int id) async {
    final response = await _dio.get(
      ApiEndpoints.boletoPdf(id),
      options: Options(responseType: ResponseType.bytes),
    );
    if (response.statusCode == 200) {
      return response.realUri.toString();
    }
    throw Exception('Error al obtener PDF');
  }
}