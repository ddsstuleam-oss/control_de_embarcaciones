<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; font-size: 10px; color: #1b1b1b; }

        .header { background: #1a1a2e; color: #fff; padding: 16px 20px; margin-bottom: 0; }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { vertical-align: middle; }
        .header .logo { width: 60px; }
        .header .logo img { width: 55px; height: auto; }
        .header .titulo { padding-left: 12px; }
        .header .titulo h1 { font-size: 14px; font-weight: 800; letter-spacing: 1px; }
        .header .titulo p { font-size: 8px; opacity: 0.8; margin-top: 2px; text-transform: uppercase; letter-spacing: 1px; }
        .header .fecha-doc { text-align: right; font-size: 8px; opacity: 0.8; }

        .accent { height: 3px; background: #e63946; }

        .meta { padding: 12px 20px; background: #f8f8f8; border-bottom: 1px solid #e0e0e0; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 8px; vertical-align: top; }
        .meta .label { font-size: 7px; text-transform: uppercase; color: #aaa; letter-spacing: 1px; font-weight: 700; }
        .meta .value { font-size: 11px; font-weight: 800; color: #1a1a2e; margin-top: 1px; }
        .meta .value-red { font-size: 12px; font-weight: 800; color: #e63946; margin-top: 1px; }

        .section-title {
            background: #1a1a2e;
            color: #fff;
            padding: 5px 20px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            margin-top: 12px;
        }

        .horas-table { width: 100%; border-collapse: collapse; }
        .horas-table td { padding: 8px 20px; font-size: 9px; border-bottom: 1px solid #f0f0f0; }
        .horas-table .h-label { color: #888; width: 33%; }
        .horas-table .h-value { font-weight: 800; color: #1a1a2e; }

        .pasajeros-table { width: 100%; border-collapse: collapse; }
        .pasajeros-table th {
            background: #f0f0f0;
            padding: 5px 8px;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #666;
            font-weight: 700;
            border-bottom: 2px solid #ddd;
            text-align: left;
        }
        .pasajeros-table td {
            padding: 5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }
        .pasajeros-table tr:nth-child(even) td { background: #fafafa; }

        .chk-si   { color: #155724; font-weight: 800; }
        .chk-no   { color: #721c24; font-weight: 800; }
        .chk-pend { color: #aaa; font-weight: 800; }

        .obs-box {
            padding: 10px 20px;
            font-size: 9px;
            color: #444;
            background: #fffbea;
            border-left: 3px solid #e63946;
            margin: 12px 20px;
        }

        .fotos-grid { padding: 8px 20px; }
        .fotos-grid table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 6px; }
        .fotos-grid .foto-cell { width: 33.33%; padding: 5px; text-align: center; vertical-align: top; }
        .fotos-grid .foto-img { width: 100%; height: auto; border: 1px solid #ddd; border-radius: 2px; }

        .estado-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 2px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .estado-finalizado { background: #cce5ff; color: #004085; }
        .estado-en_curso   { background: #fff3cd; color: #856404; }
        .estado-abordando  { background: #d4edda; color: #155724; }

        .footer {
            background: #e63946;
            color: #fff;
            padding: 6px 20px;
            text-align: center;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 16px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="logo">
                    @if($logoBase64)
                        <img src="data:image/png;base64,{{ $logoBase64 }}" alt="ULEAM">
                    @endif
                </td>
                <td class="titulo">
                    <h1>Reporte Final de Viaje</h1>
                    <p>Universidad Laica Eloy Alfaro de Manabí — FCVT</p>
                </td>
                <td class="fecha-doc">
                    <div>Generado el</div>
                    <div style="font-size:10px; font-weight:700;">{{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="accent"></div>

    <div class="meta">
        <table>
            <tr>
                <td>
                    <div class="label">Reserva</div>
                    <div class="value">#{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}</div>
                </td>
                <td>
                    <div class="label">Embarcación</div>
                    <div class="value">{{ $embarcacion->nombre }}</div>
                </td>
                <td>
                    <div class="label">Fecha</div>
                    <div class="value">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</div>
                </td>
                <td>
                    <div class="label">Estado final</div>
                    <div class="value"><span class="estado-badge estado-{{ $viaje->estado }}">{{ $viaje->estado }}</span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Responsable de la reserva</div>
                    <div class="value">{{ $reserva->user->name }}</div>
                </td>
                <td>
                    <div class="label">Operador</div>
                    <div class="value">{{ $viaje->operador->name }}</div>
                </td>
                <td>
                    <div class="label">Total pasajeros</div>
                    <div class="value-red">{{ $reserva->total_personas }}</div>
                </td>
                <td>
                    <div class="label">Duración del viaje</div>
                    <div class="value">{{ $duracion ?? '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Horarios</div>
    <table class="horas-table">
        <tr>
            <td class="h-label">Hora programada de salida</td>
            <td class="h-value">{{ $viaje->hora_programada_salida->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="h-label">Hora real de salida</td>
            <td class="h-value">{{ $viaje->hora_real_salida?->format('d/m/Y H:i') ?? '—' }}</td>
        </tr>
        <tr>
            <td class="h-label">Hora programada de llegada</td>
            <td class="h-value">{{ $viaje->hora_programada_llegada->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="h-label">Hora real de llegada</td>
            <td class="h-value">{{ $viaje->hora_real_llegada?->format('d/m/Y H:i') ?? '—' }}</td>
        </tr>
    </table>

    <div class="section-title">Checklist de Embarque y Llegada</div>
    <table class="pasajeros-table">
        <thead>
            <tr>
                <th>Pasajero</th>
                <th style="width:70px;">Cédula</th>
                <th style="width:50px;">Abordó</th>
                <th style="width:60px;">Hora abordaje</th>
                <th style="width:50px;">Llegó</th>
                <th style="width:60px;">Hora llegada</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pasajeros as $p)
            <tr>
                <td>
                    <strong>{{ $p->nombre }}</strong>
                    @if($p->tipo || $p->carrera)
                        <br>
                        <span style="font-size:8px; color:#888;">
                            {{ $p->tipo ? ucfirst($p->tipo) : '' }}{{ $p->tipo && $p->carrera ? ' · ' : '' }}{{ $p->carrera }}
                        </span>
                    @endif
                </td>
                <td style="font-family: monospace;">{{ $p->cedula }}</td>
                <td>
                    @if($p->presente === true) <span class="chk-si">Sí</span>
                    @elseif($p->presente === false) <span class="chk-no">No</span>
                    @else <span class="chk-pend">&bull;</span>
                    @endif
                </td>
                <td>{{ $p->hora_abordaje?->format('H:i') ?? '—' }}</td>
                <td>
                    @if($p->llego === true) <span class="chk-si">Sí</span>
                    @elseif($p->llego === false) <span class="chk-no">No</span>
                    @else <span class="chk-pend">&bull;</span>
                    @endif
                </td>
                <td>{{ $p->hora_llegada?->format('H:i') ?? '—' }}</td>
                <td style="font-size:8px; color:#666;">
                    {{ $p->observaciones_embarque }}
                    @if($p->observaciones_embarque && $p->observaciones_llegada) <br> @endif
                    {{ $p->observaciones_llegada }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($viaje->observaciones_cierre)
    <div class="section-title">Observaciones de cierre</div>
    <div class="obs-box">{{ $viaje->observaciones_cierre }}</div>
    @endif

    @if($fotosSalida->isNotEmpty())
    <div class="section-title">Evidencia fotográfica — Salida</div>
    <div class="fotos-grid">
        @foreach($fotosSalida->chunk(3) as $fila)
        <table>
            <tr>
                @foreach($fila as $foto)
                    <td class="foto-cell"><img class="foto-img" src="{{ $foto }}"></td>
                @endforeach
                @for($i = $fila->count(); $i < 3; $i++)
                    <td class="foto-cell">&nbsp;</td>
                @endfor
            </tr>
        </table>
        @endforeach
    </div>
    @endif

    @if($fotosLlegada->isNotEmpty())
    <div class="section-title">Evidencia fotográfica — Llegada</div>
    <div class="fotos-grid">
        @foreach($fotosLlegada->chunk(3) as $fila)
        <table>
            <tr>
                @foreach($fila as $foto)
                    <td class="foto-cell"><img class="foto-img" src="{{ $foto }}"></td>
                @endforeach
                @for($i = $fila->count(); $i < 3; $i++)
                    <td class="foto-cell">&nbsp;</td>
                @endfor
            </tr>
        </table>
        @endforeach
    </div>
    @endif

    <div class="footer">
        ULEAM — Facultad de Ciencias de la Vida y Tecnologías &bull;
        Reporte generado electrónicamente &bull;
        {{ date('Y') }}
    </div>

</body>
</html>
