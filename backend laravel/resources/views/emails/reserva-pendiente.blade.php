@extends('emails.layout')

@section('title', 'Solicitud de reserva recibida')

@section('heading')
  <span class="hl">Solicitud recibida</span>
  <span class="accent">— ULEAM</span>
@endsection

@section('content')
  <tr>
    <td class="text-body" style="padding:8px 24px 8px;font-size:14px;line-height:1.6;">
      Hola <strong class="text-strong">{{ $reserva->user->name }}</strong>, recibimos tu solicitud de reserva.
    </td>
  </tr>
  <tr>
    <td class="text-body" style="padding:0 24px 12px;font-size:14px;line-height:1.65;">
      Está <span class="hl">pendiente de aprobación</span> por parte de un administrador.
      Te notificaremos por correo apenas sea revisada.
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

  <tr>
    <td align="center" class="text-muted" style="padding:16px 24px 24px;font-size:12px;">
      No es necesario que hagas nada más por ahora — el boleto se generará automáticamente si tu reserva es aprobada.
    </td>
  </tr>
@endsection
