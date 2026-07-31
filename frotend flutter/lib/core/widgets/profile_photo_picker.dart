import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter/material.dart';
import 'package:image_cropper/image_cropper.dart';
import 'package:image_picker/image_picker.dart';
import '../theme/app_theme.dart';

/// Muestra el selector cámara/galería (mismo patrón que la evidencia de
/// viaje) y luego abre el recorte circular manual. Devuelve el archivo
/// recortado, o null si el usuario cancela en cualquier paso.
///
/// [CroppedFile] (a diferencia de `dart:io File`) sabe leer sus bytes tanto
/// en móvil como en web, así que el llamador puede subirlo sin importar la
/// plataforma.
Future<CroppedFile?> elegirYRecortarFotoPerfil(BuildContext context) async {
  // En web, abrir un modal en la misma pasada síncrona del tap (mientras el
  // mouse tracker todavía está procesando el hover/click del botón) dispara
  // una reentrancia en su actualización de dispositivo y revienta con un
  // assert, dejando la pantalla oscurecida sin el contenido del modal. Se
  // difiere un tick para dejar que ese ciclo del mouse tracker termine antes
  // de montar el modal.
  if (kIsWeb) await Future<void>.delayed(Duration.zero);
  if (!context.mounted) return null;

  final origen = await showModalBottomSheet<ImageSource>(
    context: context,
    shape: const RoundedRectangleBorder(
      borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
    ),
    builder: (_) => SafeArea(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const SizedBox(height: 10),
          Container(
            width:  40,
            height: 4,
            decoration: BoxDecoration(
              color:        Colors.grey[300],
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(height: 8),
          ListTile(
            leading: const Icon(Icons.camera_alt_outlined, color: AppTheme.primary),
            title:   const Text('Tomar fotografía'),
            onTap:   () => Navigator.pop(context, ImageSource.camera),
          ),
          ListTile(
            leading: const Icon(Icons.photo_library_outlined, color: AppTheme.primary),
            title:   const Text('Seleccionar desde la galería'),
            onTap:   () => Navigator.pop(context, ImageSource.gallery),
          ),
          const SizedBox(height: 8),
        ],
      ),
    ),
  );

  if (origen == null) return null;

  final foto = await ImagePicker().pickImage(
    source: origen,
    maxWidth:     1280,
    imageQuality: 90,
  );
  if (foto == null) return null;

  final recortada = await ImageCropper().cropImage(
    sourcePath:   foto.path,
    aspectRatio:  const CropAspectRatio(ratioX: 1, ratioY: 1),
    uiSettings: [
      AndroidUiSettings(
        toolbarTitle:       'Ajustar foto de perfil',
        toolbarColor:       AppTheme.primary,
        toolbarWidgetColor: Colors.white,
        initAspectRatio:    CropAspectRatioPreset.square,
        lockAspectRatio:    true,
        cropStyle:          CropStyle.circle,
      ),
      IOSUiSettings(
        title:                 'Ajustar foto de perfil',
        cropStyle:             CropStyle.circle,
        aspectRatioLockEnabled: true,
      ),
      // WebPresentStyle.dialog arma internamente un ButtonBar dentro de un
      // IntrinsicHeight con ancho infinito — combinación que hace crashear
      // el layout en Flutter Web (bug del propio paquete). .page usa un
      // Scaffold normal para el recorte y no tiene ese problema.
      if (kIsWeb && context.mounted)
        WebUiSettings(
          context: context,
          presentStyle: WebPresentStyle.page,
        ),
    ],
  );

  return recortada;
}
