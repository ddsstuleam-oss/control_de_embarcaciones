import 'dart:async';
import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/auth_repository.dart';
import '../../../core/api/api_interceptor.dart';

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository();
});

class AuthState {
  final Map<String, dynamic>? user;
  final bool    isLoading;
  final String? error;
  final bool    isAuthenticated;
  final bool    requiresVerification;

  const AuthState({
    this.user,
    this.isLoading            = false,
    this.error,
    this.isAuthenticated      = false,
    this.requiresVerification = false,
  });

  String? get rol       => user?['rol'];
  bool    get isAdmin    => rol == 'admin';
  bool    get isOperador => rol == 'operador';
  bool    get isUsuario  => rol == 'usuario';

  AuthState copyWith({
    Map<String, dynamic>? user,
    bool?    isLoading,
    String?  error,
    bool?    isAuthenticated,
    bool?    requiresVerification,
  }) {
    return AuthState(
      user:                 user                 ?? this.user,
      isLoading:            isLoading            ?? this.isLoading,
      error:                error,
      isAuthenticated:      isAuthenticated      ?? this.isAuthenticated,
      requiresVerification: requiresVerification ?? this.requiresVerification,
    );
  }
}

class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repo;
  final Completer<void> _readyCompleter = Completer<void>();

  /// Se resuelve cuando termina de intentar restaurar la sesión guardada
  /// (login exitoso, sesión inválida o sin sesión) — nunca antes.
  /// El splash screen debe esperar esto en vez de usar un temporizador fijo.
  Future<void> get ready => _readyCompleter.future;

  AuthNotifier(this._repo) : super(const AuthState(isLoading: true)) {
    AuthInterceptor.onUnauthorized = forceLogout;
    _init();
  }

  Future<void> _init() async {
    try {
      final isLogged = await _repo.isLoggedIn();
      if (isLogged) {
        final user = await _repo.getSavedUser();
        final emailVerified = user?['email_verified'] as bool? ?? true;
        state = AuthState(
          user:                 user,
          isAuthenticated:      true,
          isLoading:            false,
          requiresVerification: !emailVerified,
        );
      } else {
        state = const AuthState(isLoading: false);
      }
    } finally {
      if (!_readyCompleter.isCompleted) _readyCompleter.complete();
    }
  }

  /// Cierra la sesión localmente sin llamar al backend — usado cuando el
  /// interceptor detecta un 401 (token expirado/revocado) para que el resto
  /// de la app deje de mostrar datos como si la sesión siguiera activa.
  void forceLogout() {
    if (!state.isAuthenticated) return;
    state = const AuthState(isLoading: false);
  }

  /// Combina [cambios] con el usuario actual en memoria y lo persiste —
  /// usado tras actualizar datos de perfil (p.ej. la foto) para que el resto
  /// de la app vea los cambios sin necesidad de volver a loguearse.
  Future<void> actualizarUsuario(Map<String, dynamic> cambios) async {
    final user = state.user;
    if (user == null) return;
    final updated = Map<String, dynamic>.from(user)..addAll(cambios);
    await _repo.saveUser(updated);
    state = state.copyWith(user: updated);
  }

  Future<void> setVerified(Map<String, dynamic>? updatedUser) async {
    final user = updatedUser ?? state.user;
    if (user != null) {
      final updated = Map<String, dynamic>.from(user)..['email_verified'] = true;
      await _repo.saveUser(updated);
      state = state.copyWith(user: updated, requiresVerification: false);
    } else {
      state = state.copyWith(requiresVerification: false);
    }
  }

  Future<bool> login(String cedula, String password) async {
    state = const AuthState(isLoading: true);
    try {
      final data = await _repo.login(cedula: cedula, password: password);
      final user = data['user'] as Map<String, dynamic>?;
      final emailVerified = user?['email_verified'] as bool? ?? true;
      state = AuthState(
        user:                 user,
        isAuthenticated:      true,
        isLoading:            false,
        requiresVerification: !emailVerified,
      );
      return true;
    } catch (e) {
      state = AuthState(
        isLoading:       false,
        isAuthenticated: false,
        error:           _parseError(e),
      );
      return false;
    }
  }

  Future<bool> register({
    required String cedula,
    required String name,
    required String email,
    required String telefono,
    required String password,
    required String passwordConfirmation,
  }) async {
    state = const AuthState(isLoading: true);
    try {
      final data = await _repo.register(
        cedula:               cedula,
        name:                 name,
        email:                email,
        telefono:             telefono,
        password:             password,
        passwordConfirmation: passwordConfirmation,
      );

      final raw = data['requires_verification'];
      final requiresVerification =
          raw == true || raw == 1 || raw == '1' || raw == 'true';

      state = AuthState(
        user:                 data['user'],
        isAuthenticated:      true,
        isLoading:            false,
        requiresVerification: requiresVerification,
      );
      return true;
    } catch (e) {
      state = AuthState(
        isLoading:       false,
        isAuthenticated: false,
        error:           _parseError(e, isRegister: true),
      );
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _repo.logout();
    } finally {
      // Pase lo que pase con la revocación remota o el storage, la sesión
      // en memoria siempre debe quedar cerrada — si no, la UI se queda
      // "pegada" pensando que sigues autenticado.
      state = const AuthState();
    }
  }

  String _parseError(dynamic e, {bool isRegister = false}) {
    try {
      if (e is DioException) {
        final data = e.response?.data;
        if (data is Map) {
          final errors = data['errors'];
          if (errors is Map && errors.isNotEmpty) {
            final first = errors.values.first;
            if (first is List && first.isNotEmpty) {
              return first.first.toString();
            }
          }
          return data['message']?.toString() ??
                 data['error']?.toString()   ??
                 (isRegister ? 'Error al registrarse' : 'Cédula o contraseña incorrectos');
        }
      }
    } catch (_) {}
    return isRegister ? 'Error al registrarse' : 'Cédula o contraseña incorrectos';
  }
}

final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier(ref.read(authRepositoryProvider));
});