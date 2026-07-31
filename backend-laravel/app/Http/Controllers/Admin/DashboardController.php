<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Embarcacion;
use App\Models\Boleto;
use App\Models\Pasajero;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Admin — Dashboard
 *
 * Endpoints para el panel de control administrativo.
 * Proveen estadísticas en tiempo real y reportes históricos del sistema.
 * Todos los endpoints requieren rol admin.
 */
class DashboardController extends Controller
{
    /**
     * Resumen general
     *
     * Retorna un resumen completo del estado actual del sistema:
     * totales generales, actividad del día, mes actual, próximas reservas y ocupación por embarcación.
     * La ocupación se calcula con una sola query optimizada para evitar N+1.
     *
     * @authenticated
     *
     * @response 200 scenario="Éxito" {
     *   "totales": {
     *     "embarcaciones": 4,
     *     "disponibles": 3,
     *     "mantenimiento": 1,
     *     "usuarios": 5,
     *     "reservas_total": 10,
     *     "pasajeros_total": 25
     *   },
     *   "hoy": {
     *     "reservas": 2,
     *     "pasajeros": 5,
     *     "canceladas": 0
     *   },
     *   "mes_actual": {
     *     "reservas": 8,
     *     "pasajeros": 20,
     *     "canceladas": 1
     *   },
     *   "proximas_reservas": [
     *     {
     *       "id": 1,
     *       "fecha": "2026-04-13",
     *       "embarcacion": "Lancha Uleam I",
     *       "usuario": "Estudiante Test",
     *       "personas": 2,
     *       "estado": "confirmada"
     *     }
     *   ],
     *   "ocupacion_hoy": [
     *     {
     *       "id": 1,
     *       "nombre": "Lancha Uleam I",
     *       "capacidad": 25,
     *       "estado": "disponible",
     *       "reservados": 10,
     *       "disponibles": 15,
     *       "porcentaje": 40.0
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $hoy  = now()->toDateString();
        $mes  = now()->month;
        $anio = now()->year;

        $reservasHoy = Reserva::with('embarcacion')
            ->whereDate('fecha', $hoy)
            ->whereNotIn('estado', ['cancelada'])
            ->orderBy('hora_inicio')
            ->get();

        $idsConReserva = $reservasHoy->pluck('embarcacion_id')->unique();

        $ocupacionConReservas = $reservasHoy->map(fn($r) => [
            'id'          => $r->embarcacion_id,
            'nombre'      => $r->embarcacion->nombre,
            'capacidad'   => $r->embarcacion->capacidad,
            'estado'      => $r->embarcacion->estado,
            'hora_inicio' => $r->hora_inicio,
            'hora_fin'    => $r->hora_fin,
            'reservados'  => $r->total_personas,
            'disponibles' => max(0, $r->embarcacion->capacidad - $r->total_personas),
            'porcentaje'  => $r->embarcacion->capacidad > 0
                ? round($r->total_personas / $r->embarcacion->capacidad * 100, 1)
                : 0,
        ]);

        $ocupacionSinReservas = Embarcacion::get()
            ->filter(fn($e) => !$idsConReserva->contains($e->id))
            ->map(fn($e) => [
                'id'          => $e->id,
                'nombre'      => $e->nombre,
                'capacidad'   => $e->capacidad,
                'estado'      => $e->estado,
                'hora_inicio' => null,
                'hora_fin'    => null,
                'reservados'  => 0,
                'disponibles' => $e->capacidad,
                'porcentaje'  => 0.0,
            ])
            ->values();

        $ocupacionHoy = $ocupacionConReservas->concat($ocupacionSinReservas);

        return response()->json([

            'pendientes_aprobacion' => Reserva::where('estado', 'pendiente')->count(),

            'totales' => [
                'embarcaciones'   => Embarcacion::count(),
                'disponibles'     => Embarcacion::where('estado', 'disponible')->count(),
                'mantenimiento'   => Embarcacion::where('estado', 'mantenimiento')->count(),
                'usuarios'        => User::count(),
                'reservas_total'  => Reserva::count(),
                'pasajeros_total' => Pasajero::count(),
            ],

            'hoy' => [
                'reservas'   => Reserva::whereDate('fecha', $hoy)
                    ->whereNotIn('estado', ['cancelada'])
                    ->count(),
                'pasajeros'  => Reserva::whereDate('fecha', $hoy)
                    ->whereNotIn('estado', ['cancelada'])
                    ->sum('total_personas'),
                'canceladas' => Reserva::whereDate('fecha', $hoy)
                    ->where('estado', 'cancelada')
                    ->count(),
            ],

            'mes_actual' => [
                'reservas'   => Reserva::whereMonth('fecha', $mes)
                    ->whereYear('fecha', $anio)
                    ->whereNotIn('estado', ['cancelada'])
                    ->count(),
                'pasajeros'  => Reserva::whereMonth('fecha', $mes)
                    ->whereYear('fecha', $anio)
                    ->whereNotIn('estado', ['cancelada'])
                    ->sum('total_personas'),
                'canceladas' => Reserva::whereMonth('fecha', $mes)
                    ->whereYear('fecha', $anio)
                    ->where('estado', 'cancelada')
                    ->count(),
            ],

            'proximas_reservas' => Reserva::with('embarcacion', 'user')
                ->where('fecha', '>=', $hoy)
                ->whereNotIn('estado', ['cancelada'])
                ->orderBy('fecha')
                ->limit(10)
                ->get()
                ->map(fn($r) => [
                    'id'          => $r->id,
                    'fecha'       => $r->fecha,
                    'embarcacion' => $r->embarcacion->nombre,
                    'usuario'     => $r->user->name,
                    'personas'    => $r->total_personas,
                    'estado'      => $r->estado,
                ]),

            'ocupacion_hoy' => $ocupacionHoy,
        ]);
    }

    /**
     * Estadísticas por rango de fechas
     *
     * Retorna estadísticas detalladas filtradas por rango de fechas.
     * Por defecto retorna el mes actual.
     * Incluye: reservas por día, por embarcación, por tipo de pasajero, por estado y top usuarios.
     *
     * @authenticated
     *
     * @queryParam desde string Fecha de inicio (YYYY-MM-DD). Default: primer día del mes actual. Example: 2026-04-01
     * @queryParam hasta string Fecha de fin (YYYY-MM-DD). Default: hoy. Example: 2026-04-30
     *
     * @response 200 scenario="Éxito" {
     *   "rango": {
     *     "desde": "2026-04-01",
     *     "hasta": "2026-04-30"
     *   },
     *   "por_dia": [
     *     {"fecha": "2026-04-13", "total_reservas": 2, "total_pasajeros": 5}
     *   ],
     *   "por_embarcacion": [
     *     {"embarcacion": "Lancha Uleam I", "total_reservas": 5, "total_pasajeros": 12}
     *   ],
     *   "por_tipo": [
     *     {"tipo": "estudiante", "total": 8},
     *     {"tipo": "externo", "total": 3}
     *   ],
     *   "por_estado": [
     *     {"estado": "confirmada", "total": 8},
     *     {"estado": "cancelada", "total": 1}
     *   ],
     *   "top_usuarios": [
     *     {"nombre": "Estudiante Test", "email": "estudiante@uleam.edu.ec", "total_reservas": 3}
     *   ]
     * }
     * @response 422 scenario="Fechas inválidas" {
     *   "message": "Error de validación",
     *   "errors": {"hasta": ["The hasta field must be a date after or equal to desde."]}
     * }
     */
    public function estadisticas(Request $request)
    {
        $request->validate([
            'desde' => 'sometimes|date',
            'hasta' => 'sometimes|date|after_or_equal:desde',
        ]);

        $desde = $request->desde ?? now()->startOfMonth()->toDateString();
        $hasta = $request->hasta ?? now()->toDateString();

        $porDia = Reserva::selectRaw('fecha, COUNT(*) as total_reservas, SUM(total_personas) as total_pasajeros')
            ->whereBetween('fecha', [$desde, $hasta])
            ->whereNotIn('estado', ['cancelada'])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $porEmbarcacion = Embarcacion::withCount([
                'reservas as total_reservas' => fn($q) => $q
                    ->whereBetween('fecha', [$desde, $hasta])
                    ->whereNotIn('estado', ['cancelada']),
            ])
            ->withSum([
                'reservas as total_pasajeros' => fn($q) => $q
                    ->whereBetween('fecha', [$desde, $hasta])
                    ->whereNotIn('estado', ['cancelada']),
            ], 'total_personas')
            ->get()
            ->map(fn($e) => [
                'embarcacion'     => $e->nombre,
                'total_reservas'  => $e->total_reservas,
                'total_pasajeros' => $e->total_pasajeros ?? 0,
            ]);

        $porTipo = Pasajero::selectRaw('tipo, COUNT(*) as total')
            ->whereHas('reserva', fn($q) => $q
                ->whereBetween('fecha', [$desde, $hasta])
                ->whereNotIn('estado', ['cancelada'])
            )
            ->groupBy('tipo')
            ->get();

        $porEstado = Reserva::selectRaw('estado, COUNT(*) as total')
            ->whereBetween('fecha', [$desde, $hasta])
            ->groupBy('estado')
            ->get();

        $topUsuarios = User::withCount([
                'reservas as total_reservas' => fn($q) => $q
                    ->whereBetween('fecha', [$desde, $hasta])
                    ->whereNotIn('estado', ['cancelada']),
            ])
            ->orderByDesc('total_reservas')
            ->limit(5)
            ->get(['id', 'name', 'email'])
            ->map(fn($u) => [
                'nombre'         => $u->name,
                'email'          => $u->email,
                'total_reservas' => $u->total_reservas,
            ]);

        return response()->json([
            'rango'           => ['desde' => $desde, 'hasta' => $hasta],
            'por_dia'         => $porDia,
            'por_embarcacion' => $porEmbarcacion,
            'por_tipo'        => $porTipo,
            'por_estado'      => $porEstado,
            'top_usuarios'    => $topUsuarios,
        ]);
    }
}