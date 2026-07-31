<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1b1b1b;
            background: #fff;
        }

        .ticket {
            width: 100%;
            overflow: hidden;
            border: 1px solid #c0c0c0;
        }

        /* ===== HEADER BLANCO CON LOGO ===== */
        .ticket-header {
            background: #d2f0c4;
            padding: 12px 18px;
            border-bottom: 2px solid #e0e0e0;
        }

        .header-layout {
            width: 100%;
            border-collapse: collapse;
        }

        .header-layout td {
            vertical-align: middle;
        }

        .logo-area {
            width: 70px;
        }

        .logo-area img {
            width: 62px;
            height: auto;
        }

        .header-info {
            padding-left: 10px;
        }

        .header-title {
            font-size: 13px;
            font-weight: 800;
            color: #1b1b1b;
            letter-spacing: 0.5px;
        }

        .header-sub {
            font-size: 8px;
            color: #000000;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .header-badge {
            text-align: right;
            width: 70px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .badge-valido {
            background: #00c853;
            color: #fff;
        }

        .badge-usado {
            background: #ff9800;
            color: #fff;
        }

        .badge-cancelado {
            background: #f44336;
            color: #fff;
        }

        /* ===== BARRA ROJA DECORATIVA ===== */
        .accent-bar {
            height: 3px;
            background: #d32f2f;
        }

        /* Título boarding pass */
        .boarding-title {
            background: #f7f7f7;
            padding: 8px 18px;
            border-bottom: 1px solid #e0e0e0;
        }

        .boarding-title-table {
            width: 100%;
            border-collapse: collapse;
        }

        .boarding-title-table td {
            vertical-align: middle;
        }

        .bt-left {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #333;
        }

        .bt-right {
            text-align: right;
            font-size: 9px;
            color: #999;
        }

        .reserva-num {
            font-size: 14px;
            font-weight: 800;
            color: #d32f2f;
            letter-spacing: 1px;
        }

        /* ===== DATOS PRINCIPALES ===== */
        .main-data {
            padding: 14px 18px 10px;
        }

        .route-display {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .route-display td {
            vertical-align: top;
            padding: 0;
        }

        .route-from, .route-to {
            width: 42%;
        }

        .route-arrow {
            width: 16%;
            text-align: center;
            vertical-align: middle;
            padding-top: 8px;
        }

        .route-code {
            font-size: 22px;
            font-weight: 800;
            color: #1b1b1b;
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .route-label {
            font-size: 8px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .route-detail {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .dotted-sep {
            border: none;
            border-top: 2px dotted #d0d0d0;
            margin: 10px 0;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .info-grid td {
            padding: 5px 0;
            vertical-align: top;
            width: 33.33%;
        }

        .info-label {
            font-size: 7px;
            text-transform: uppercase;
            color: #aaa;
            letter-spacing: 1.2px;
            font-weight: 700;
            margin-bottom: 1px;
        }

        .info-value {
            font-size: 12px;
            font-weight: 700;
            color: #1b1b1b;
        }

        .info-value-big {
            font-size: 16px;
            font-weight: 800;
            color: #d32f2f;
        }

        /* ===== PASAJEROS ===== */
        .passengers-section {
            padding: 0 18px 10px;
        }

        .passengers-header {
            font-size: 7px;
            text-transform: uppercase;
            color: #aaa;
            letter-spacing: 1.2px;
            font-weight: 700;
            margin-bottom: 4px;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
        }

        .pax-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pax-table td {
            padding: 3px 0;
            font-size: 10px;
            border-bottom: 1px solid #f5f5f5;
        }

        .pax-num {
            width: 20px;
            font-weight: 800;
            color: #d32f2f;
            font-size: 10px;
        }

        .pax-name {
            color: #222;
            font-weight: 600;
        }

        .pax-ci {
            text-align: right;
            color: #888;
            font-size: 9px;
            font-family: 'Courier New', monospace;
        }

        /* ===== QR + TEAR LINE ===== */
        .tear-line {
            border: none;
            border-top: 2px dashed #ccc;
            margin: 0;
        }

        .qr-section {
            background: #fafafa;
            padding: 10px 18px;
            text-align: center;
        }

        .qr-section img {
            width: 90px;
            height: 90px;
        }

        .qr-code-text {
            font-size: 8px;
            color: #bbb;
            margin-top: 3px;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
            font-weight: 700;
        }

        .qr-instruction {
            font-size: 7px;
            color: #ccc;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== FOOTER ROJO ===== */
        .ticket-footer {
            background: #d32f2f;
            padding: 6px 18px;
            text-align: center;
            font-size: 7px;
            color: #fff;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="ticket">

    {{-- HEADER BLANCO CON LOGO --}}
    <div class="ticket-header">
        <table class="header-layout">
            <tr>
                <td class="logo-area">
                    <img src="data:image/png;base64,{{ $logoBase64 }}" alt="ULEAM">
                </td>
                <td class="header-info">
                    <div class="header-title">Control de Embarcaciones</div>
                    <div class="header-sub">Universidad Laica Eloy Alfaro de Manab&iacute;</div>
                </td>
                <td class="header-badge">
                    @if($boleto->estado === 'valido')
                        <span class="badge badge-valido">V&aacute;lido</span>
                    @elseif($boleto->estado === 'usado')
                        <span class="badge badge-usado">Usado</span>
                    @else
                        <span class="badge badge-cancelado">{{ strtoupper($boleto->estado) }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="accent-bar"></div>

    {{-- TÍTULO --}}
    <div class="boarding-title">
        <table class="boarding-title-table">
            <tr>
                <td class="bt-left">Boleto de Embarque</td>
                <td class="bt-right">
                    <div style="font-size: 7px; color: #aaa; text-transform: uppercase; letter-spacing: 1px;">N&deg; Reserva</div>
                    <div class="reserva-num">#{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- RUTA / DATOS PRINCIPALES --}}
    <div class="main-data">

        <table class="route-display">
            <tr>
                <td class="route-from">
                    <div class="route-label">Embarcaci&oacute;n</div>
                    <div class="route-code">{{ $embarcacion->nombre }}</div>
                    <div class="route-detail">Capacidad: {{ $embarcacion->capacidad }} personas</div>
                </td>
                
</td>
                <td class="route-to">
                    <div class="route-label">Fecha de viaje</div>
                    <div class="route-code">
    {{ \Carbon\Carbon::parse($reserva->fecha)
        ->timezone('America/Guayaquil')
        ->format('d/m') }}
</div>

<div class="route-detail">
    {{ \Carbon\Carbon::parse($reserva->fecha)
        ->locale('es')
        ->timezone('America/Guayaquil')
        ->isoFormat('dddd, D [de] MMMM YYYY') }}
</div>
                </td>
            </tr>
        </table>

        <hr class="dotted-sep">

        <table class="info-grid">
            <tr>
                <td>
                    <div class="info-label">Titular</div>
                    <div class="info-value">{{ $usuario->name }}</div>
                </td>
                <td>
                    <div class="info-label">Pasajeros</div>
                    <div class="info-value-big">{{ $reserva->total_personas }}</div>
                </td>
                @if($reserva->hora_inicio)
                <td>
                    <div class="info-label">Horario</div>
                    <div class="info-value">{{ substr($reserva->hora_inicio, 0, 5) }} — {{ substr($reserva->hora_fin, 0, 5) }}</div>
                </td>
                @else
                <td>
                    <div class="info-label">Emitido</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($boleto->created_at)
    ->timezone('America/Guayaquil')
    ->format('d/m/Y') }}</div>
                </td>
                @endif
            </tr>
        </table>

    </div>

    <hr class="dotted-sep" style="margin: 0 18px;">

    {{-- PASAJEROS --}}
    <div class="passengers-section" style="padding-top: 10px;">
        <div class="passengers-header">Listado de Pasajeros</div>
        <table class="pax-table">
            @foreach($pasajeros as $i => $p)
            <tr>
                <td class="pax-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                <td class="pax-name">{{ strtoupper($p->nombre) }}</td>
                <td class="pax-ci">{{ $p->cedula }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    {{-- TEAR LINE + QR --}}
    <hr class="tear-line">

    <div class="qr-section">
        <img src="data:image/svg+xml;base64,{{ $qrBase64 }}" alt="QR">
        <div class="qr-code-text">{{ $boleto->codigo_qr }}</div>
        <div class="qr-instruction">Presente este c&oacute;digo al momento del embarque</div>
    </div>

    {{-- FOOTER ROJO --}}
    <div class="ticket-footer">
        V&aacute;lido &uacute;nicamente para la fecha indicada &bull;
        ULEAM &copy; {{ date('Y') }} &bull;
        Documento electr&oacute;nico &bull;
        No requiere firma
    </div>

</div>
</body>
</html>