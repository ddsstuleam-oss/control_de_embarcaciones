import 'package:dio/dio.dart';
import 'api_endpoints.dart';
import 'api_interceptor.dart';

class ApiClient {
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;
  ApiClient._internal();

  late final Dio dio = Dio(
    BaseOptions(
      baseUrl:        ApiEndpoints.baseUrl,
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 15),
      validateStatus: (status) => status! < 500,
    ),
  )
    ..interceptors.add(MethodOverrideInterceptor())
    ..interceptors.add(AuthInterceptor());
}