import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/theme/app_theme.dart';
import '../../../../features/auth/providers/auth_provider.dart';
import '../../../../features/viajes/data/viaje_repository.dart';

class SplashScreen extends ConsumerStatefulWidget {
  const SplashScreen({super.key});

  @override
  ConsumerState<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends ConsumerState<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double>   _fadeIn;

  @override
  void initState() {
    super.initState();

    // Pantalla completa sin barras — no aplica en navegador
    if (!kIsWeb) {
      SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);
    }

    _controller = AnimationController(
      vsync:    this,
      duration: const Duration(milliseconds: 1200),
    );

    _fadeIn = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _controller, curve: Curves.easeInOut),
    );

    _controller.forward();

    _iniciar();
  }

  Future<void> _iniciar() async {
    if (kIsWeb) {
      // En web no hay marca/splash que lucir — solo se espera lo necesario
      // para saber si ya había una sesión guardada.
      await ref.read(authProvider.notifier).ready;
    } else {
      // Duración mínima de marca + espera real a que termine de restaurarse
      // la sesión guardada (ya no un tiempo fijo "a ojo" que puede no alcanzar
      // en un dispositivo lento).
      await Future.wait([
        Future.delayed(const Duration(milliseconds: 1500)),
        ref.read(authProvider.notifier).ready,
      ]);
    }

    if (!mounted) return;

    if (!kIsWeb) {
      SystemChrome.setEnabledSystemUIMode(
        SystemUiMode.edgeToEdge,
        overlays: SystemUiOverlay.values,
      );
    }

    final authState = ref.read(authProvider);
    if (!authState.isAuthenticated) {
      context.go('/welcome');
      return;
    }

    final home = switch (authState.rol) {
      'admin'    => '/admin/dashboard',
      'operador' => '/operador',
      _          => '/home',
    };

    // Si es operador/admin y quedó un único viaje en curso, ofrecer
    // retomarlo directamente en vez de solo dejarlo en la lista del home.
    if (authState.isOperador || authState.isAdmin) {
      final viajeId = await _buscarViajeUnicoActivo();
      if (!mounted) return;
      if (viajeId != null) {
        final continuar = await _preguntarRetomarViaje();
        if (!mounted) return;
        if (continuar) {
          context.go(home);
          context.push('/viajes/$viajeId');
          return;
        }
      }
    }

    context.go(home);
  }

  Future<int?> _buscarViajeUnicoActivo() async {
    try {
      // propio: true — el aviso de "continuar viaje" es siempre personal,
      // sin importar el rol: solo debe salir a quien escaneó el QR.
      final activos = await ViajeRepository().getActivos(propio: true);
      return activos.length == 1 ? activos.first['id'] as int : null;
    } catch (_) {
      return null;
    }
  }

  Future<bool> _preguntarRetomarViaje() async {
    final resultado = await showDialog<bool>(
      context: context,
      barrierDismissible: false,
      builder: (_) => AlertDialog(
        title:   const Text('Viaje en curso'),
        content: const Text(
            'Tienes un viaje que quedó en curso. ¿Deseas continuarlo donde lo dejaste?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Ir al inicio'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppTheme.primary),
            child: const Text('Continuar viaje'),
          ),
        ],
      ),
    );
    return resultado ?? false;
  }

  @override
  void dispose() {
    _controller.dispose();
    if (!kIsWeb) {
      SystemChrome.setEnabledSystemUIMode(
        SystemUiMode.edgeToEdge,
        overlays: SystemUiOverlay.values,
      );
    }
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (kIsWeb) {
      // Sin marca a pantalla completa en web — solo una carga breve y
      // neutra mientras se resuelve si ya había sesión guardada.
      return const Scaffold(
        backgroundColor: AppTheme.primary,
        body: Center(
          child: CircularProgressIndicator(color: Colors.white),
        ),
      );
    }

    return Scaffold(
      body: FadeTransition(
        opacity: _fadeIn,
        child: SizedBox.expand(
          child: Image.asset(
            'assets/images/splash_marino.png',
            fit: BoxFit.cover,
          ),
        ),
      ),
    );
  }
}
