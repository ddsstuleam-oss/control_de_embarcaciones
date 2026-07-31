@extends('emails.layout')

@section('title', 'Alerta de retraso en viaje')

@section('heading')
  <span class="hl">Retraso detectado</span>
  <span class="accent">— ULEAM</span>
@endsection

@section('content')
  <tr>
    <td class="text-body" style="padding:8px 24px 8px;font-size:14px;line-height:1.6;">
      El viaje <strong class="text-strong">#{{ $viaje->id }}</strong> de la embarcación
      <strong class="text-strong">{{ $viaje->embarcacion->nombre }}</strong> lleva más de
      <strong class="text-strong">60 minutos</strong> de retraso sobre su hora programada de
      llegada y aún no se ha registrado la llegada real.
    </td>
  </tr>

  <tr>
    <td style="padding:0 24px 16px;">
      <div class="info-box">
        <div class="info-row">
          <span class="info-label">N° Reserva</span>
          <span class="info-value">#{{ str_pad($viaje->reserva->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Responsable</span>
          <span class="info-value">{{ $viaje->reserva->user->name }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Operador</span>
          <span class="info-value">{{ $viaje->operador->name }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Hora programada de llegada</span>
          <span class="info-value">{{ $viaje->hora_programada_llegada->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Hora real de salida</span>
          <span class="info-value">{{ $viaje->hora_real_salida?->format('d/m/Y H:i') ?? '—' }}</span>
        </div>
      </div>
    </td>
  </tr>

  <tr>
    <td align="center" class="text-muted" style="padding:0 24px 24px;font-size:12px;">
      Revisa el estado del viaje en el panel de administración.
    </td>
  </tr>
@endsection
