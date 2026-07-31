@extends('emails.layout')

@section('title', 'Reserva confirmada')

@section('heading')
  <span class="hl">¡Reserva confirmada!</span>
  <span class="accent">— ULEAM</span>
@endsection

@section('content')
  <tr>
    <td class="text-body" style="padding:8px 24px 8px;font-size:14px;line-height:1.6;">
      Hola <strong class="text-strong">{{ $reserva->user->name }}</strong>, tu reserva ha sido registrada exitosamente.
    </td>
  </tr>

  <tr>
    <td style="padding:0 24px 16px;">
      <span class="badge">CONFIRMADA</span>
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
    <td class="text-muted" style="padding:0 24px 8px;font-size:12px;text-transform:uppercase;letter-spacing:1px;">
      Pasajeros registrados
    </td>
  </tr>
  <tr>
    <td style="padding:0 24px 8px;">
      @foreach($reserva->pasajeros as $i => $p)
      <div class="divider text-strong" style="padding:8px 0;font-size:14px;">
        <strong>{{ $i + 1 }}. {{ $p->nombre }}</strong>
        <span class="text-muted" style="margin-left:8px;">CI: {{ $p->cedula }}</span>
        @if($p->tipo)
          <span class="text-muted" style="margin-left:8px;">· {{ ucfirst($p->tipo) }}</span>
        @endif
      </div>
      @endforeach
    </td>
  </tr>

  <!-- Código del boleto -->
  <tr>
    <td align="center" class="text-muted" style="padding:16px 24px 6px;font-size:13px;">
      Código de tu boleto — preséntalo al embarcar
    </td>
  </tr>
  <tr>
    <td align="center" style="padding:0 24px 6px;">
      <div class="token" style="font-size:15px;word-break:break-all;">
        {{ $reserva->boleto->codigo_qr }}
      </div>
    </td>
  </tr>
  <tr>
    <td align="center" class="text-muted" style="padding:0 24px 24px;font-size:12px;">
      El operador escaneará este código al momento del embarque
    </td>
  </tr>
@endsection
