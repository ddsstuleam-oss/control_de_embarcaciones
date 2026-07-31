import 'dart:async';
import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/services.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../features/auth/providers/auth_provider.dart';

/// Cierra la sesión automáticamente tras un período sin actividad — solo en
/// web (`kIsWeb`), porque ahí sí es común dejar una pestaña abierta en un
/// equipo compartido. La app nativa no tiene este límite: el celular ya
/// tiene su propio bloqueo (PIN/huella) protegiendo el acceso.
class InactivityGuard extends ConsumerStatefulWidget {
  final Widget child;

  static const Duration timeout = Duration(minutes: 30);

  const InactivityGuard({super.key, required this.child});

  @override
  ConsumerState<InactivityGuard> createState() => _InactivityGuardState();
}

class _InactivityGuardState extends ConsumerState<InactivityGuard> {
  Timer? _timer;

  @override
  void initState() {
    super.initState();
    if (kIsWeb) {
      HardwareKeyboard.instance.addHandler(_onKeyEvent);
      _resetTimer();
    }
  }

  @override
  void dispose() {
    _timer?.cancel();
    if (kIsWeb) {
      HardwareKeyboard.instance.removeHandler(_onKeyEvent);
    }
    super.dispose();
  }

  bool _onKeyEvent(KeyEvent event) {
    _resetTimer();
    return false; // no consumir el evento — solo lo usamos como señal
  }

  void _resetTimer() {
    _timer?.cancel();
    if (!ref.read(authProvider).isAuthenticated) return;
    _timer = Timer(InactivityGuard.timeout, _onTimeout);
  }

  void _onTimeout() {
    ref.read(authProvider.notifier).logout();
  }

  @override
  Widget build(BuildContext context) {
    if (!kIsWeb) return widget.child;

    // Reinicia (o cancela) el temporizador cuando cambia el estado de
    // sesión — p. ej. al iniciar sesión, o si otra pestaña/dispositivo ya
    // la cerró.
    ref.listen<AuthState>(authProvider, (previous, next) {
      if (next.isAuthenticated != previous?.isAuthenticated) _resetTimer();
    });

    return Listener(
      behavior:        HitTestBehavior.translucent,
      onPointerDown:   (_) => _resetTimer(),
      onPointerSignal: (_) => _resetTimer(),
      child: widget.child,
    );
  }
}
