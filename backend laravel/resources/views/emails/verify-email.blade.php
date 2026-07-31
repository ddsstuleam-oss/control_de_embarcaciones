@extends('emails.layout')

@section('title', 'Verifica tu correo')

@section('heading')
  <span class="hl">Verifica tu correo</span>
  <span class="accent">— ULEAM</span>
@endsection

@section('content')
  <tr>
    <td class="text-body" style="padding:8px 24px 8px;font-size:14px;line-height:1.6;">
      ¡Hola <strong class="text-strong">{{ $name ?? 'Estudiante' }}</strong>!
    </td>
  </tr>
  <tr>
    <td class="text-body" style="padding:0 24px 8px;font-size:14px;line-height:1.65;">
      Gracias por registrarte en <span class="hl">Embarcaciones_app</span>. Para activar tu cuenta,
      ingresa el siguiente <span class="hl">código de verificación</span>.
    </td>
  </tr>

  <!-- Código -->
  <tr>
    <td align="center" style="padding:16px 24px 6px;">
      <div class="token">
        {{ $code }}
      </div>
    </td>
  </tr>
  <tr>
    <td align="center" class="text-muted" style="padding:0 24px 14px;font-size:12px;">
      Este código expira en {{ $expires ?? 30 }} minutos.
    </td>
  </tr>

  <!-- Nota -->
  <tr>
    <td class="text-muted" style="padding:0 24px 24px;font-size:12px;line-height:1.6;">
      Si no solicitaste esta cuenta, puede ignorar este mensaje.
      Para más información, escriba a <a href="mailto:{{ $support ?? 'soporte@uleam.edu.ec' }}">{{ $support ?? 'soporte@uleam.edu.ec' }}</a>.
    </td>
  </tr>
@endsection
