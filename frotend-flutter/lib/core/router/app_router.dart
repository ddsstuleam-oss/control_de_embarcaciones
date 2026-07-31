import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../widgets/admin_nav.dart';
import 'package:uleam_embarcaciones/features/admin/perfil/presentation/screens/admin_perfil_screen.dart';
import 'package:uleam_embarcaciones/features/auth/presentation/screens/change_password_required_screen.dart';
import 'package:uleam_embarcaciones/features/auth/presentation/screens/telefono_requerido_screen.dart';
import 'package:uleam_embarcaciones/features/auth/presentation/screens/splash_screen.dart';
import 'package:uleam_embarcaciones/features/home/presentation/screens/home_screen.dart';
import 'package:uleam_embarcaciones/features/operador/presentation/screens/operador_home_screen.dart';
import 'package:uleam_embarcaciones/features/operador/presentation/screens/operador_perfil_screen.dart';
import 'package:uleam_embarcaciones/features/operador/presentation/screens/operador_reservas_screen.dart';
import 'package:uleam_embarcaciones/features/perfil/presentation/screens/actividad_screen.dart';
import '../../features/auth/providers/auth_provider.dart';
import '../../features/auth/presentation/screens/welcome_screen.dart';
import '../../features/auth/presentation/screens/login_screen.dart';
import '../../features/auth/presentation/screens/register_screen.dart';
import '../../features/auth/presentation/screens/forgot_password_screen.dart';
import '../../features/embarcaciones/presentation/screens/embarcaciones_screen.dart';
import '../../features/reservas/presentation/screens/reservas_screen.dart';
import '../../features/reservas/presentation/screens/crear_reserva_screen.dart';
import '../../features/reservas/presentation/screens/detalle_reserva_screen.dart';
import '../../features/boletos/presentation/screens/boleto_screen.dart';
import '../../features/boletos/presentation/screens/scanner_screen.dart';
import '../../features/viajes/presentation/screens/viaje_screen.dart';
import '../../features/perfil/presentation/screens/perfil_screen.dart';
import '../../features/admin/dashboard/presentation/screens/dashboard_screen.dart';
import '../../features/admin/usuarios/presentation/screens/usuarios_screen.dart';
import '../../features/admin/reportes/presentation/screens/reportes_screen.dart';
import '../../features/admin/embarcaciones/presentation/screens/admin_embarcaciones_screen.dart';
import '../../features/admin/reservas/presentation/screens/admin_reservas_screen.dart';
import '../../features/ubicacion/presentation/screens/como_llegar_screen.dart';
import '../../features/admin/directorio/presentation/screens/directorio_screen.dart';
import '../../features/auth/presentation/screens/verify_email_screen.dart';

final appRouterProvider = Provider<GoRouter>((ref) {
  final notifier = _AuthNotifier(ref);

  final router = GoRouter(
    initialLocation:   '/',
    refreshListenable: notifier,
    redirect: (context, state) {
      final authState = notifier.authState;
      final isAuth    = authState.isAuthenticated;
      final isLoading = authState.isLoading;
      final loc       = state.matchedLocation;

      if (isLoading) return null;

      // No redirigir en splash — maneja su propia navegación
      if (loc == '/') return null;

      // Usuario autenticado con email sin verificar → forzar a /verify-email
      if (isAuth && authState.requiresVerification && loc != '/verify-email') {
        return '/verify-email';
      }
      // Permitir /verify-email solo si sigue autenticado (flujo de verificación)
      if (loc == '/verify-email') {
        return isAuth ? null : '/welcome';
      }

      const publicRoutes = [
        '/welcome',
        '/login',
        '/register',
        '/forgot-password',
        '/como-llegar',
        '/cambiar-password',
      ];
      final isPublic = publicRoutes.contains(loc);

      if (isPublic) {
        return isAuth ? _homeByRole(authState.rol) : null;
      }

      return isAuth ? null : '/welcome';
    },
    routes: [
      // ── SPLASH ────────────────────────────────────────────────
      GoRoute(
        path:    '/',
        builder: (_, __) => const SplashScreen(),
      ),

      // ── AUTH ──────────────────────────────────────────────────
      GoRoute(
        path:    '/welcome',
        builder: (_, __) => const WelcomeScreen(),
      ),
      GoRoute(
        path:    '/login',
        builder: (_, __) => const LoginScreen(),
      ),
      GoRoute(
        path:    '/register',
        builder: (_, __) => const RegisterScreen(),
      ),
      GoRoute(
        path:    '/forgot-password',
        builder: (_, __) => const ForgotPasswordScreen(),
      ),
      GoRoute(
        path:    '/verify-email',
        builder: (_, __) => const VerifyEmailScreen(),
      ),
      GoRoute(
        path: '/cambiar-password',
        builder: (_, state) {
          final dias = int.tryParse(
            state.uri.queryParameters['dias'] ?? '0',
          ) ?? 0;
          return ChangePasswordRequiredScreen(diasRestantes: dias);
        },
      ),
      GoRoute(
        path: '/completar-telefono',
        builder: (_, __) => const TelefonoRequeridoScreen(),
      ),

      // ── USUARIO ───────────────────────────────────────────────
      GoRoute(
        path:    '/home',
        builder: (_, __) => const HomeScreen(),
      ),
      GoRoute(
        path:    '/embarcaciones',
        builder: (_, __) => const EmbarcacionesScreen(),
      ),
      GoRoute(
        path:    '/reservas',
        builder: (_, __) => const ReservasScreen(),
      ),
      GoRoute(
        path: '/reservas/crear',
        builder: (_, state) {
          final embarcacionId = int.tryParse(
            state.uri.queryParameters['embarcacion_id'] ?? '',
          );
          return CrearReservaScreen(embarcacionId: embarcacionId);
        },
      ),
      GoRoute(
        path: '/reservas/:id',
        builder: (_, state) => DetalleReservaScreen(
          id: int.parse(state.pathParameters['id']!),
        ),
      ),
      GoRoute(
        path: '/boletos/:id',
        builder: (_, state) => BoletoScreen(
          id: int.parse(state.pathParameters['id']!),
        ),
      ),
      GoRoute(
        path:    '/perfil',
        builder: (_, __) => const PerfilScreen(),
      ),
      GoRoute(
        path:    '/perfil/actividad',
        builder: (_, __) => const ActividadScreen(),
      ),

      // ── PÚBLICO ───────────────────────────────────────────────
      GoRoute(
        path:    '/como-llegar',
        builder: (_, __) => const ComoLlegarScreen(),
      ),

      // ── OPERADOR ──────────────────────────────────────────────
      GoRoute(
        path:    '/operador',
        builder: (_, __) => const OperadorHomeScreen(),
      ),
      GoRoute(
        path:    '/operador/perfil',
        builder: (_, __) => const OperadorPerfilScreen(),
      ),
      GoRoute(
        path:    '/operador/reservas',
        builder: (_, __) => const OperadorReservasScreen(),
      ),
      GoRoute(
        path:    '/scanner',
        builder: (_, __) => const ScannerScreen(),
      ),
      GoRoute(
        path: '/viajes/:id',
        builder: (_, state) => ViajeScreen(
          id: int.parse(state.pathParameters['id']!),
        ),
      ),

      // ── ADMIN ─────────────────────────────────────────────────
      // En web las rutas /admin/* viven dentro de un ShellRoute: el sidebar
      // y topbar de AdminShell quedan montados una sola vez y las pantallas
      // solo cambian el contenido. En móvil se mantienen como rutas planas,
      // cada pantalla con su propio Scaffold + AdminDrawer (comportamiento
      // sin cambios).
      if (kIsWeb)
        ShellRoute(
          builder: (context, state, child) =>
              AdminShell(location: state.matchedLocation, child: child),
          routes: [
            GoRoute(
              path:    '/admin/dashboard',
              builder: (_, __) => const DashboardScreen(),
            ),
            GoRoute(
              path:    '/admin/usuarios',
              builder: (_, __) => const UsuariosScreen(),
            ),
            GoRoute(
              path:    '/admin/reportes',
              builder: (_, __) => const ReportesScreen(),
            ),
            GoRoute(
              path:    '/admin/embarcaciones',
              builder: (_, __) => const AdminEmbarcacionesScreen(),
            ),
            GoRoute(
              path:    '/admin/reservas',
              builder: (_, __) => const AdminReservasScreen(),
            ),
            GoRoute(
              path:    '/admin/perfil',
              builder: (_, __) => const AdminPerfilScreen(),
            ),
            GoRoute(
              path:    '/admin/directorio',
              builder: (_, __) => const DirectorioScreen(),
            ),
          ],
        )
      else ...[
        GoRoute(
          path:    '/admin/dashboard',
          builder: (_, __) => const DashboardScreen(),
        ),
        GoRoute(
          path:    '/admin/usuarios',
          builder: (_, __) => const UsuariosScreen(),
        ),
        GoRoute(
          path:    '/admin/reportes',
          builder: (_, __) => const ReportesScreen(),
        ),
        GoRoute(
          path:    '/admin/embarcaciones',
          builder: (_, __) => const AdminEmbarcacionesScreen(),
        ),
        GoRoute(
          path:    '/admin/reservas',
          builder: (_, __) => const AdminReservasScreen(),
        ),
        GoRoute(
          path:    '/admin/perfil',
          builder: (_, __) => const AdminPerfilScreen(),
        ),
        GoRoute(
          path:    '/admin/directorio',
          builder: (_, __) => const DirectorioScreen(),
        ),
      ],
    ],
  );

  ref.onDispose(router.dispose);
  return router;
});

class _AuthNotifier extends ChangeNotifier {
  final Ref _ref;
  AuthState _authState;

  _AuthNotifier(this._ref) : _authState = _ref.read(authProvider) {
    _ref.listen<AuthState>(authProvider, (previous, next) {
      if (previous?.isAuthenticated != next.isAuthenticated) {
        _authState = next;
        notifyListeners();
      } else {
        _authState = next;
      }
    });
  }

  AuthState get authState => _authState;
}

String _homeByRole(String? rol) {
  switch (rol) {
    case 'admin':    return '/admin/dashboard';
    case 'operador': return '/operador';
    default:         return '/home';
  }
}