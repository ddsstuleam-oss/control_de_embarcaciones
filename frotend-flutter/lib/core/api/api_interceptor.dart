import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart' show kIsWeb;
import '../storage/secure_storage.dart';

/// Algunos hostings compartidos (WAF/mod_security de Apache en cPanel)
/// bloquean con 403 los métodos PATCH/PUT/DELETE por política de seguridad
/// por defecto, aun cuando el backend los acepta. Para evitarlo, estos
/// métodos se reenvían como POST con el header estándar que Laravel/Symfony
/// reconoce para restaurar el método real del lado del servidor.
class MethodOverrideInterceptor extends Interceptor {
  @override
  void onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) {
    final method = options.method.toUpperCase();
    if (method == 'PATCH' || method == 'PUT' || method == 'DELETE') {
      options.headers['X-HTTP-Method-Override'] = method;
      options.method = 'POST';
    }
    super.onRequest(options, handler);
  }
}

class AuthInterceptor extends Interceptor {
  /// Se invoca cuando el backend responde 401 (token expirado o revocado),
  /// para que el resto de la app (estado de sesión en memoria) se entere de
  /// inmediato en vez de quedarse desincronizada del storage. Lo asigna
  /// AuthNotifier al crearse.
  static void Function()? onUnauthorized;

  @override
  Future<void> onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await SecureStorage.getToken();
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    options.headers['Accept']       = 'application/json';
    options.headers['Content-Type'] = 'application/json';
    // El backend nombra el token según esto para saber a cuáles aplicarles
    // el límite de inactividad (solo web) — ver EnsureWebSessionNotIdle.
    if (kIsWeb) options.headers['X-Client-Platform'] = 'web';
    super.onRequest(options, handler);
  }

  @override
  void onResponse(Response response, ResponseInterceptorHandler handler) {
    // ApiClient configura `validateStatus: status < 500`, así que un 401
    // llega aquí como respuesta "exitosa" (no como DioException) — por eso
    // el chequeo también hace falta en onResponse, no solo en onError.
    if (response.statusCode == 401) {
      SecureStorage.clear();
      onUnauthorized?.call();
    }
    super.onResponse(response, handler);
  }

  @override
  void onError(DioException err, ErrorInterceptorHandler handler) {
    if (err.response?.statusCode == 401) {
      SecureStorage.clear();
      onUnauthorized?.call();
    }
    super.onError(err, handler);
  }
}