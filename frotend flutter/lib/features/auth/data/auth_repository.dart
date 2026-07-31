import 'package:dio/dio.dart';
import '../../../core/api/api_client.dart';
import '../../../core/api/api_endpoints.dart';
import '../../../core/storage/secure_storage.dart';
import 'dart:convert';

class AuthRepository {
  final _dio = ApiClient().dio;

  Future<Map<String, dynamic>> login({
    required String cedula,
    required String password,
  }) async {
    final response = await _dio.post(
      ApiEndpoints.login,
      data: {'cedula': cedula, 'password': password},
    );

    if (response.statusCode == 200) {
      final data = response.data;
      await SecureStorage.saveToken(data['token']);
      await SecureStorage.saveUser(jsonEncode(data['user']));
      return data;
    }

    throw DioException(
      requestOptions: response.requestOptions,
      response: response,
      message: response.data['message'] ?? 'Error al iniciar sesión',
    );
  }

  Future<Map<String, dynamic>> register({
    required String cedula,
    required String name,
    required String email,
    required String telefono,
    required String password,
    required String passwordConfirmation,
  }) async {
    final response = await _dio.post(
      ApiEndpoints.register,
      data: {
        'cedula'                : cedula,
        'name'                  : name,
        'email'                 : email,
        'telefono'              : telefono,
        'password'              : password,
        'password_confirmation' : passwordConfirmation,
      },
    );

    final status = response.statusCode ?? 0;
    if (status >= 200 && status < 300) {
      final data = response.data;
      await SecureStorage.saveToken(data['token']);
      await SecureStorage.saveUser(jsonEncode(data['user']));
      return data;
    }

    throw DioException(
      requestOptions: response.requestOptions,
      response: response,
      message: response.data['message'] ?? 'Error al registrarse',
    );
  }

  Future<void> logout() async {
    try {
      await _dio.post(ApiEndpoints.logout);
    } catch (_) {
      // Si falla la revocación en el servidor (red, token ya vencido, CORS,
      // etc.) igual cerramos la sesión localmente — es lo que el usuario
      // pidió y lo que importa del lado del cliente.
    } finally {
      await SecureStorage.clear();
    }
  }

  Future<Map<String, dynamic>> me() async {
    final response = await _dio.get(ApiEndpoints.me);
    if (response.statusCode == 200) {
      return response.data['user'];
    }
    throw DioException(
      requestOptions: response.requestOptions,
      response: response,
    );
  }

  Future<void> forgotPassword(String email) async {
    final response = await _dio.post(
      ApiEndpoints.forgotPassword,
      data: {'email': email},
    );
    if (response.statusCode != 200) {
      throw DioException(
        requestOptions: response.requestOptions,
        response: response,
        message: response.data['message'] ?? 'Error al enviar correo',
      );
    }
  }

  Future<void> resetPassword({
    required String email,
    required String token,
    required String password,
    required String passwordConfirmation,
  }) async {
    final response = await _dio.post(
      ApiEndpoints.resetPassword,
      data: {
        'email'                 : email,
        'token'                 : token,
        'password'              : password,
        'password_confirmation' : passwordConfirmation,
      },
    );
    if (response.statusCode != 200) {
      throw DioException(
        requestOptions: response.requestOptions,
        response: response,
        message: response.data['message'] ?? 'Error al restablecer contraseña',
      );
    }
  }

  Future<bool> isLoggedIn() async {
    final token = await SecureStorage.getToken();
    return token != null;
  }

  Future<Map<String, dynamic>?> getSavedUser() async {
    final userJson = await SecureStorage.getUser();
    if (userJson == null) return null;
    return jsonDecode(userJson);
  }

  Future<void> saveUser(Map<String, dynamic> user) async {
    await SecureStorage.saveUser(jsonEncode(user));
  }
}