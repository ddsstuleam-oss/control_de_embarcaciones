import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_cropper/image_cropper.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';

class PerfilRepository {
  final _dio = ApiClient().dio;

  Future<Map<String, dynamic>> getPerfil() async {
    final response = await _dio.get(ApiEndpoints.perfil);
    if (response.statusCode == 200) return response.data;
    throw Exception('Error al cargar perfil');
  }

  Future<Map<String, dynamic>> subirFotoPerfil(CroppedFile foto) async {
    final bytes = await foto.readAsBytes();
    final formData = FormData.fromMap({
      'foto': MultipartFile.fromBytes(
        bytes,
        filename: foto.path.split('/').last,
      ),
    });

    final response = await _dio.post(
      ApiEndpoints.perfilFoto,
      data: formData,
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(response.data['error'] ?? 'Error al subir la foto de perfil');
  }

  Future<Map<String, dynamic>> actualizarPerfil({
    String? name,
    String? email,
    String? telefono,
  }) async {
    final response = await _dio.put(
      ApiEndpoints.perfil,
      data: {
        if (name != null) 'name': name,
        if (email != null) 'email': email,
        if (telefono != null) 'telefono': telefono,
      },
    );
    if (response.statusCode == 200) return response.data;
    throw Exception(
      response.data['message'] ?? response.data['error'] ?? 'Error al actualizar el perfil',
    );
  }

  Future<void> cambiarPassword({
    required String currentPassword,
    required String newPassword,
    required String confirmation,
  }) async {
    final response = await _dio.post(
      ApiEndpoints.updatePassword,
      data: {
        'current_password'      : currentPassword,
        'new_password'          : newPassword,
        'new_password_confirmation': confirmation,
      },
    );
    if (response.statusCode != 200) {
      throw Exception(
        response.data['error'] ?? response.data['message'] ?? 'Error',
      );
    }
  }
}

final perfilRepositoryProvider = Provider<PerfilRepository>((ref) {
  return PerfilRepository();
});

final perfilProvider = FutureProvider.autoDispose<Map<String, dynamic>>((ref) {
  return ref.read(perfilRepositoryProvider).getPerfil();
});

final subiendoFotoPerfilProvider = StateProvider.autoDispose<bool>((ref) => false);