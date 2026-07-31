<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boleto extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'reserva_id',
        'codigo_qr',
        'pdf_url',
        'estado',
        'usado_en',
    ];

    protected $casts = [
        'usado_en' => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    /**
     * Código corto para el boleto: 8 caracteres alfanuméricos en mayúsculas,
     * sin los que se confunden fácilmente al escribirlos a mano (0/O, 1/I/L)
     * — el operador puede validar un boleto tanto escaneando el QR como
     * tipeando este código manualmente.
     */
    public static function generarCodigo(): string
    {
        $alfabeto = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';
            for ($i = 0; $i < 8; $i++) {
                $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
        } while (self::where('codigo_qr', $codigo)->exists());

        return $codigo;
    }

    /**
     * Un boleto está vencido cuando sigue "valido" pero la ventana de embarque
     * (hora_inicio + 30 min de la reserva) ya pasó según la hora real actual.
     */
    public function estaVencido(): bool
    {
        if ($this->estado !== 'valido' || !$this->reserva) {
            return false;
        }

        $ventanaFin = \Carbon\Carbon::parse(
            $this->reserva->fecha->toDateString() . ' ' . $this->reserva->hora_inicio
        )->addMinutes(30);

        return now()->gt($ventanaFin);
    }

    /**
     * Estado del boleto calculado en tiempo real: si ya venció pero la BD
     * todavía no fue actualizada (p. ej. el job programado no ha corrido),
     * refleja "vencido" igual para quien lo consulta.
     */
    public function getEstadoEnVivoAttribute(): string
    {
        return $this->estaVencido() ? 'vencido' : $this->estado;
    }

    /**
     * Persiste el boleto como vencido y, si la reserva seguía "confirmada",
     * la marca también como "vencida" (libera el horario y refleja el estado
     * real en cualquier pantalla que muestre la reserva).
     */
    public function marcarVencido(): void
    {
        $this->update(['estado' => 'vencido']);

        if ($this->reserva && $this->reserva->estado === 'confirmada') {
            $this->reserva->update(['estado' => 'vencida']);
        }
    }
}