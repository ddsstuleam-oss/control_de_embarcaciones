import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_native_splash/flutter_native_splash.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'core/theme/app_theme.dart';
import 'core/router/app_router.dart';
import 'core/widgets/connectivity_wrapper.dart';
import 'core/widgets/inactivity_guard.dart';

void main() async {
  // ← Preservar el splash mientras inicializa
  final binding = WidgetsFlutterBinding.ensureInitialized();
  FlutterNativeSplash.preserve(widgetsBinding: binding);

  // App bloqueada solo en vertical (también fijado a nivel nativo en
  // AndroidManifest.xml e Info.plist, esto cubre web/desktop de sobra).
  await SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);

  await initializeDateFormatting('es', null);

  runApp(
    const ProviderScope(
      child: UleamApp(),
    ),
  );
}

class UleamApp extends ConsumerWidget {
  const UleamApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final router = ref.watch(appRouterProvider);

    // ← Remover splash cuando la app esté lista
    FlutterNativeSplash.remove();

    return MaterialApp.router(
      title:                      'ULEAM Embarcaciones',
      debugShowCheckedModeBanner: false,
      theme:                      AppTheme.light,
      routerConfig:               router,
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      supportedLocales: const [
        Locale('es'),
        Locale('en'),
      ],
      locale: const Locale('es'),

      builder: (context, child) => InactivityGuard(
        child: ConnectivityWrapper(
          child: child ?? const SizedBox.shrink(),
        ),
      ),
    );
  }
}