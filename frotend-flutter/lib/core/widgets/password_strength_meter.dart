import 'package:flutter/material.dart';
import '../theme/app_theme.dart';

/// 0 = débil (no cumple el mínimo), 1 = medio (cumple el mínimo), 2 = fuerte
/// (además tiene un símbolo o es bastante larga). Refleja las mismas reglas
/// que valida el backend (Password::min(8)->mixedCase()->numbers()), así que
/// si esto marca "medio" o "fuerte" el registro no debería rechazarla por
/// fortaleza.
int passwordStrength(String password) {
  final tieneMinuscula = RegExp(r'[a-z]').hasMatch(password);
  final teneMayuscula = RegExp(r'[A-Z]').hasMatch(password);
  final tieneNumero = RegExp(r'[0-9]').hasMatch(password);
  final tieneSimbolo = RegExp(r'[^a-zA-Z0-9]').hasMatch(password);

  final cumpleMinimo =
      password.length >= 8 && tieneMinuscula && teneMayuscula && tieneNumero;

  if (!cumpleMinimo) return 0;
  if (tieneSimbolo || password.length >= 12) return 2;
  return 1;
}

class PasswordStrengthMeter extends StatelessWidget {
  final String password;
  const PasswordStrengthMeter({super.key, required this.password});

  @override
  Widget build(BuildContext context) {
    if (password.isEmpty) return const SizedBox.shrink();

    final nivel = passwordStrength(password);
    const colores = [AppTheme.error, AppTheme.warning, AppTheme.success];
    const etiquetas = ['Débil', 'Media', 'Fuerte'];
    final color = colores[nivel];

    return Padding(
      padding: const EdgeInsets.only(top: 8),
      child: Row(
        children: [
          for (var i = 0; i < 3; i++) ...[
            if (i > 0) const SizedBox(width: 4),
            Expanded(
              child: ClipRRect(
                borderRadius: BorderRadius.circular(3),
                child: LinearProgressIndicator(
                  value: 1,
                  minHeight: 5,
                  backgroundColor: Colors.grey[200],
                  valueColor: AlwaysStoppedAnimation(
                    i <= nivel ? color : Colors.grey[200]!,
                  ),
                ),
              ),
            ),
          ],
          const SizedBox(width: 8),
          Text(
            etiquetas[nivel],
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w600,
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}
