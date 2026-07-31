@extends('emails.layout')

@section('title', 'Recuperación de contraseña')

@section('heading')
  <span class="hl">Embarcaciones_app</span>
  <span class="accent">— ULEAM</span>
@endsection

@section('content')
  <tr>
    <td class="text-body" style="padding:8px 24px 8px;font-size:14px;line-height:1.6;">
      Estimado/a: <strong class="text-strong">{{ $name ?? 'ESTUDIANTE' }}</strong>
    </td>
  </tr>
  <tr>
    <td class="text-body" style="padding:0 24px 8px;font-size:14px;line-height:1.65;">
      Hemos recibido una solicitud para restablecer su contraseña de
      <span class="hl">Embarcaciones_app</span>. A continuación, le enviamos el
      <span class="hl">código</span> necesario para continuar.
    </td>
  </tr>

  <!-- Código -->
  <tr>
    <td align="center" style="padding:16px 24px 6px;">
      <div class="token">
        {{ $token }}
      </div>
    </td>
  </tr>
  <tr>
    <td align="center" class="text-muted" style="padding:0 24px 14px;font-size:12px;">
      Este código expira en {{ $expires ?? 60 }} minutos.
    </td>
  </tr>

  @if(!empty($actionUrl))
  <tr>
    <td align="center" style="padding:4px 24px 20px;">
      <a href="{{ $actionUrl }}" target="_blank" rel="noopener"
         style="background:#e11d48;color:#fff;padding:12px 18px;border-radius:12px;font-weight:700;display:inline-block;">
         Restablecer ahora
      </a>
    </td>
  </tr>
  @endif

  <!-- Nota -->
  <tr>
    <td class="text-muted" style="padding:0 24px 24px;font-size:12px;line-height:1.6;">
      Si usted no solicitó este cambio, puede ignorar este mensaje.
      Para más información, escriba a <a href="mailto:{{ $support ?? 'soporte@uleam.edu.ec' }}">{{ $support ?? 'soporte@uleam.edu.ec' }}</a>.
    </td>
  </tr>
@endsection
