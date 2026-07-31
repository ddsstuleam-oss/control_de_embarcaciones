import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/boleto_repository.dart';

final boletoRepositoryProvider = Provider<BoletoRepository>((ref) {
  return BoletoRepository();
});

final boletoProvider =
    FutureProvider.autoDispose.family<Map<String, dynamic>, int>((ref, id) {
  return ref.read(boletoRepositoryProvider).getBoleto(id);
});