@extends('emails.layout')

@section('title', 'Reserva cancelada')

@section('heading')
  <span class="hl">Reserva cancelada</span>
  <span class="accent">— ULEAM</span>
@endsection

@section('content')
  <tr>
    <td class="text-body" style="padding:8px 24px 8px;font-size:14px;line-height:1.6;">
      Hola <strong class="text-strong">{{ $reserva->user->name }}</strong>, tu reserva ya confirmada fue cancelada por un administrador.
    </td>
  </tr>
  <tr>
    <td class="text-body" style="padding:0 24px 12px;font-size:14px;line-height:1.65;">
      El boleto asociado quedó invalidado. El horario ha quedado liberado y puedes realizar una nueva
      reserva para otra fecha u horario disponible.
    </td>
  </tr>

  <tr>
    <td style="padding:0 24px 16px;">
      <div class="info-box">
        <div class="info-row">
          <span class="info-label">N° Reserva</span>
          <span class="info-value">#{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>
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
          <span class="info-label">Total pasajeros</span>
          <span class="info-value">{{ $reserva->total_personas }}</span>
        </div>
      </div>
    </td>
  </tr>

  @if($reserva->motivo_cancelacion)
  <tr>
    <td style="padding:0 24px 16px;">
      <div class="info-box">
        <div class="info-row" style="display:block;border-bottom:none;">
          <span class="info-label">Motivo de la cancelación</span>
        </div>
        <div class="text-body" style="font-size:13px;padding-top:4px;">
          {{ $reserva->motivo_cancelacion }}
        </div>
      </div>
    </td>
  </tr>
  @endif

  <tr>
    <td align="center" class="text-muted" style="padding:0 24px 24px;font-size:12px;">
      Por favor, realiza una nueva solicitud de reserva para otra fecha u horario.
    </td>
  </tr>
@endsection
