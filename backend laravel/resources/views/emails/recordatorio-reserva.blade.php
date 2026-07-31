@extends('emails.layout')

@section('title', 'Recordatorio de embarque')

@section('heading')
  <span class="hl">Recordatorio de embarque</span>
  <span class="accent">— ULEAM</span>
@endsection

@section('content')
  <tr>
    <td class="text-body" style="padding:8px 24px 8px;font-size:14px;line-height:1.6;">
      Hola <strong class="text-strong">{{ $reserva->user->name }}</strong>,
    </td>
  </tr>
  <tr>
    <td class="text-body" style="padding:0 24px 12px;font-size:14px;line-height:1.65;">
      Te recordamos que tienes una reserva programada para <span class="hl">mañana</span>.
      Ten listo tu código QR al momento de embarcar.
    </td>
  </tr>

  <tr>
    <td style="padding:0 24px 16px;">
      <div class="info-box">
        <div class="info-row">
          <span class="info-label">Embarcación</span>
          <span class="info-value">{{ $reserva->embarcacion->nombre }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Fecha</span>
          <span class="info-value">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</span>
        </div>
        @if($reserva->hora_inicio)
        <div class="info-row">
          <span class="info-label">Horario</span>
          <span class="info-value">{{ substr($reserva->hora_inicio, 0, 5) }} — {{ substr($reserva->hora_fin, 0, 5) }}</span>
        </div>
        @endif
        <div class="info-row">
          <span class="info-label">Pasajeros</span>
          <span class="info-value">{{ $reserva->total_personas }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">N° Reserva</span>
          <span class="info-value">#{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
      </div>
    </td>
  </tr>

  <!-- Código de embarque -->
  <tr>
    <td align="center" class="text-muted" style="padding:16px 24px 6px;font-size:13px;">
      Tu código de embarque
    </td>
  </tr>
  <tr>
    <td align="center" style="padding:0 24px 6px;">
      <div class="token">
        {{ $reserva->boleto->codigo_qr }}
      </div>
    </td>
  </tr>
  <tr>
    <td align="center" class="text-muted" style="padding:0 24px 24px;font-size:12px;">
      Presenta este código al operador en el puerto
    </td>
  </tr>
@endsection
