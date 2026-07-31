import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../providers/connectivity_provider.dart';
import '../theme/app_theme.dart';
import 'no_internet_widget.dart';

class ConnectivityWrapper extends ConsumerWidget {
  final Widget child;

  const ConnectivityWrapper({super.key, required this.child});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final connectivityAsync = ref.watch(connectivityProvider);

    return connectivityAsync.when(
      loading: () => child,
      error:   (_, __) => child,
      data: (hasInternet) {
        if (!hasInternet) {
          return Scaffold(
            backgroundColor: AppTheme.background,
            body: NoInternetWidget(
              onRetry: () => ref.invalidate(connectivityProvider),
            ),
          );
        }
        return child;
      },
    );
  }
}