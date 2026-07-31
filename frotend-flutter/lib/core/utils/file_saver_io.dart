import 'dart:io';
import 'package:open_filex/open_filex.dart';
import 'package:path_provider/path_provider.dart';

/// Guarda [bytes] como archivo temporal y lo abre con el visor nativo
/// (PDF, Excel, etc.) — Android/iOS/desktop.
Future<void> guardarYAbrirBytes(List<int> bytes, String filename) async {
  final dir  = await getTemporaryDirectory();
  final file = File('${dir.path}/$filename');
  await file.writeAsBytes(bytes);
  await OpenFilex.open(file.path);
}
