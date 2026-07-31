import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/embarcacion_repository.dart';

final embarcacionRepositoryProvider = Provider<EmbarcacionRepository>((ref) {
  return EmbarcacionRepository();
});

final embarcacionesProvider = FutureProvider.autoDispose<List<Map<String, dynamic>>>((ref) {
  return ref.read(embarcacionRepositoryProvider).getEmbarcaciones();
});

final disponibilidadProvider = FutureProvider.family<
    List<Map<String, dynamic>>,
    String>(
  (ref, fecha) {
    return ref.read(embarcacionRepositoryProvider).getDisponibilidad(fecha);
  },
);

final embarcacionProvider =
    FutureProvider.family<Map<String, dynamic>, int>((ref, id) {
  return ref.read(embarcacionRepositoryProvider).getEmbarcacion(id);
});