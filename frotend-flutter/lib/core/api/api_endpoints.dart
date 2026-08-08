import 'package:flutter/foundation.dart' show kIsWeb, kReleaseMode;

class ApiEndpoints {
  // Web corre en el navegador, así que "localhost" apunta a la propia
  // máquina; el emulador de Android en cambio necesita el alias 10.0.2.2
  // para llegar al backend que corre en el host. En producción, el
  // `.htaccess` de raíz ya recorta el prefijo "/api" antes de que la
  // petición llegue a Laravel (por eso allá `apiPrefix` está vacío en
  // `bootstrap/app.php`); `php artisan serve` en local no tiene esa capa,
  // así que hay que pegarle a las rutas sin el "/api" para que coincidan.
  static const String baseUrl = kReleaseMode
      ? 'https://uleamembarcaciones.com/api'
      : (kIsWeb ? 'http://localhost:8000' : 'http://10.0.2.2:8000');

  // Auth
  static const String login          = '/login';
  static const String register       = '/register';
  static const String logout         = '/logout';
  static const String me             = '/me';
  static const String updatePassword = '/update-password';
  static const String forgotPassword = '/forgot-password';
  static const String resetPassword  = '/reset-password';
  static const String verifyEmail        = '/email/verify';
  static const String resendVerification = '/email/resend';

  // Embarcaciones
  static const String embarcaciones  = '/embarcaciones';
  static const String disponibilidad = '/disponibilidad';
  static String embarcacion(int id)  => '/embarcaciones/$id';
  static String horariosOcupados(int id) => '/embarcaciones/$id/horarios';

  // Admin embarcaciones
  static const String adminEmbarcaciones       = '/admin/embarcaciones';
  static String adminEmbarcacion(int id)       => '/admin/embarcaciones/$id';
  

  // Reservas
  static const String reservas       = '/reservas';
  static const String reservasFecha  = '/reservas-fecha';
  static const String reservasHoy    = '/reservas-hoy';
  static String reserva(int id)      => '/reservas/$id';
  static String cancelarReserva(int id) => '/reservas/$id/cancelar';

  // Pasajeros (edición al momento del embarque)
  static String reservaPasajeros(int reservaId) => '/reservas/$reservaId/pasajeros';
  static String reservaPasajero(int reservaId, int pasajeroId) =>
      '/reservas/$reservaId/pasajeros/$pasajeroId';

  // Control Operativo del Viaje
  static const String viajesActivos            = '/viajes-activos';
  static String viajeEscanear(String codigo)   => '/viajes/escanear/$codigo';
  static String viaje(int id)                  => '/viajes/$id';
  static String viajeChecklistEmbarque(int id) => '/viajes/$id/checklist-embarque';
  static String viajeEvidencias(int id)        => '/viajes/$id/evidencias';
  static String viajeEliminarEvidencia(int id, int evidenciaId) =>
      '/viajes/$id/evidencias/$evidenciaId';
  static String viajeIniciar(int id)           => '/viajes/$id/iniciar';
  static String viajeChecklistLlegada(int id)  => '/viajes/$id/checklist-llegada';
  static String viajeRegistrarLlegada(int id)  => '/viajes/$id/registrar-llegada';
  static String viajeFinalizar(int id)         => '/viajes/$id/finalizar';
  static String viajeReportePdf(int id)        => '/viajes/$id/reporte-pdf';

  // Admin reservas
  static const String adminReservas            = '/admin/reservas';
  static String adminAprobarReserva(int id)    => '/admin/reservas/$id/aprobar';
  static String adminRechazarReserva(int id)   => '/admin/reservas/$id/rechazar';
  static String adminCancelarReserva(int id)   => '/admin/reservas/$id/cancelar';
  static String adminEliminarReserva(int id)   => '/admin/reservas/$id';
  static String adminRestaurarReserva(int id)  => '/admin/reservas/$id/restore';

  // Boletos
  static String boleto(int id)                      => '/boletos/$id';
  static String boletoPdf(int id)                   => '/boletos/$id/pdf';
  static String validarBoleto(String codigo)        => '/validar-boleto/$codigo';
  static String embarcarBoleto(int id)              => '/boletos/$id/embarcar';

  // Perfil
  static const String perfil               = '/perfil';
  static const String perfilFoto           = '/perfil/foto';
  static const String perfilReservas       = '/perfil/reservas';
  static const String perfilActividades    = '/perfil/actividades';
  static const String perfilEmail          = '/perfil/email';
  static const String perfilEmailConfirmar = '/perfil/email/confirmar';

  // Actividad
  static const String actividad = '/actividad';

  // Admin
  static const String adminDashboard     = '/admin/dashboard';
  static const String adminEstadisticas  = '/admin/estadisticas';

  // Admin usuarios
  static const String adminUsuarios             = '/admin/usuarios';
  static String adminUsuario(int id)            => '/admin/usuarios/$id';
  static String adminToggleUsuario(int id)      => '/admin/usuarios/$id/toggle';
  static String adminRolUsuario(int id)         => '/admin/usuarios/$id/rol';

  // Reportes
  static const String reporteExcelPasajeros = '/admin/reportes/excel-pasajeros';
  static const String reporteExcelReservas  = '/admin/reportes/excel-reservas';
  static const String reportePdfManifiesto  = '/admin/reportes/pdf-manifiesto';

  // Directorio
  static const String directorioBuscar         = '/directorio/buscar';
  static const String adminDirectorio          = '/admin/directorio';
  static const String adminDirectorioImportar  = '/admin/directorio/importar';
  static const String adminDirectorioPlantilla = '/admin/directorio/plantilla';

  
}