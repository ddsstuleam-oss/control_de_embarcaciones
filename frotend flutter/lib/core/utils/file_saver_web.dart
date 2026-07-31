// ignore_for_file: avoid_web_libraries_in_flutter
import 'dart:html' as html;
import 'dart:typed_data';

/// Dispara la descarga de [bytes] en el navegador con el nombre [filename] —
/// no hay filesystem ni visor nativo en web, así que el navegador se encarga
/// (se guarda en la carpeta de Descargas del usuario).
Future<void> guardarYAbrirBytes(List<int> bytes, String filename) async {
  final blob = html.Blob([Uint8List.fromList(bytes)]);
  final url  = html.Url.createObjectUrlFromBlob(blob);
  html.AnchorElement(href: url)
    ..setAttribute('download', filename)
    ..click();
  html.Url.revokeObjectUrl(url);
}
