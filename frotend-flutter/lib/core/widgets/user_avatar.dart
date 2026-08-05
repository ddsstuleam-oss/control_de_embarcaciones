import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

/// Círculo de avatar con inicial o foto de perfil. Cuando [editable] es true
/// muestra una insignia de cámara (abajo a la derecha) para cambiar la foto —
/// y si ya hay foto, tocar el círculo la previsualiza en pantalla completa en
/// vez de abrir el selector (el ícono de cámara sigue siendo el atajo directo
/// para cambiarla). También admite [roleBadge] (abajo a la izquierda) para
/// insignias de rol como las que ya usan las pantallas de operador/admin.
class UserAvatar extends StatelessWidget {
  final String   nombre;
  final String?  fotoUrl;
  final double   radius;
  final Color    backgroundColor;
  final bool     editable;
  final bool     uploading;
  final VoidCallback? onTap;
  final Widget?  roleBadge;

  const UserAvatar({
    super.key,
    required this.nombre,
    this.fotoUrl,
    this.radius = 40,
    this.backgroundColor = AppTheme.accent,
    this.editable = false,
    this.uploading = false,
    this.onTap,
    this.roleBadge,
  });

  @override
  Widget build(BuildContext context) {
    final tieneFoto = fotoUrl != null && fotoUrl!.isNotEmpty;
    final previsualizable = editable && tieneFoto && !uploading;

    final avatar = CircleAvatar(
      radius:          radius,
      backgroundColor: backgroundColor,
      backgroundImage: tieneFoto
          ? CachedNetworkImageProvider(
              fotoUrl!,
              maxWidth:  (radius * 2 * 3).round(),
              maxHeight: (radius * 2 * 3).round(),
            )
          : null,
      onBackgroundImageError: tieneFoto ? (_, __) {} : null,
      child: tieneFoto
          ? null
          : Text(
              nombre.isNotEmpty ? nombre[0].toUpperCase() : 'U',
              style: TextStyle(
                color:      Colors.white,
                fontSize:   radius * 0.8,
                fontWeight: FontWeight.w700,
              ),
            ),
    );

    if (!editable && roleBadge == null) return avatar;

    return SizedBox(
      width:  radius * 2 + 8,
      height: radius * 2 + 8,
      child: Stack(
        clipBehavior: Clip.none,
        children: [
          Positioned(
            left: 4,
            top:  4,
            child: GestureDetector(
              onTap: !uploading
                  ? (previsualizable
                      ? () => _mostrarPreview(context, fotoUrl!)
                      : (editable ? onTap : null))
                  : null,
              child: ClipOval(
                child: SizedBox(
                  width:  radius * 2,
                  height: radius * 2,
                  child: Stack(
                    children: [
                      avatar,
                      if (uploading)
                        Container(
                          color: Colors.black45,
                          child: const Center(
                            child: SizedBox(
                              width:  24,
                              height: 24,
                              child: CircularProgressIndicator(
                                color:       Colors.white,
                                strokeWidth: 2,
                              ),
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              ),
            ),
          ),
          if (roleBadge != null)
            Positioned(bottom: 0, left: 0, child: roleBadge!),
          if (editable)
            Positioned(
              bottom: 0,
              right:  0,
              child: GestureDetector(
                onTap: !uploading ? onTap : null,
                child: Container(
                  padding: const EdgeInsets.all(6),
                  decoration: BoxDecoration(
                    color:  AppTheme.primary,
                    shape:  BoxShape.circle,
                    border: Border.all(color: Colors.white, width: 2),
                  ),
                  child: const Icon(
                    Icons.camera_alt,
                    color: Colors.white,
                    size:  14,
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }

  void _mostrarPreview(BuildContext context, String fotoUrl) {
    Navigator.of(context).push(
      PageRouteBuilder(
        opaque: false,
        barrierColor: Colors.black87,
        pageBuilder: (_, __, ___) => _PreviewFotoPerfil(fotoUrl: fotoUrl),
      ),
    );
  }
}

class _PreviewFotoPerfil extends StatelessWidget {
  final String fotoUrl;
  const _PreviewFotoPerfil({required this.fotoUrl});

  @override
  Widget build(BuildContext context) {
    final tamano = MediaQuery.of(context).size.shortestSide * 0.75;

    return Scaffold(
      backgroundColor: Colors.transparent,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation:        0,
        iconTheme:        const IconThemeData(color: Colors.white),
      ),
      body: GestureDetector(
        onTap: () => Navigator.of(context).pop(),
        child: Center(
          child: InteractiveViewer(
            minScale: 1,
            maxScale: 4,
            child: ClipOval(
              child: CachedNetworkImage(
                imageUrl: fotoUrl,
                width:    tamano,
                height:   tamano,
                fit:      BoxFit.cover,
                memCacheWidth:  (tamano * 3).round(),
                memCacheHeight: (tamano * 3).round(),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
