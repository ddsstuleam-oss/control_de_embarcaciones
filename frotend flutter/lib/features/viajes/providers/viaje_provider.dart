import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/viaje_repository.dart';

final viajeRepositoryProvider = Provider<ViajeRepository>((ref) => ViajeRepository());

final viajeDetalleProvider =
    FutureProvider.autoDispose.family<Map<String, dynamic>, int>((ref, id) async {
  return ref.read(viajeRepositoryProvider).getViaje(id);
});
