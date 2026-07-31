import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/reserva_repository.dart';

final reservaRepositoryProvider = Provider<ReservaRepository>((ref) {
  return ReservaRepository();
});

final misReservasProvider = FutureProvider.autoDispose<List<Map<String, dynamic>>>((ref) {
  return ref.read(reservaRepositoryProvider).getMisReservas();
});

final reservaDetalleProvider =
    FutureProvider.autoDispose.family<Map<String, dynamic>, int>((ref, id) {
  return ref.read(reservaRepositoryProvider).getReserva(id);
});