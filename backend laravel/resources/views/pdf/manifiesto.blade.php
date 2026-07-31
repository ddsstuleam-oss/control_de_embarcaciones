<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 10px; color: #1b1b1b; }

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
        .meta .value { font-size: 12px; font-weight: 800; color: #1a1a2e; margin-top: 1px; }
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

        .reservas-table { width: 100%; border-collapse: collapse; margin: 0; }
        .reservas-table th {
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
        .reservas-table td {
            padding: 5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }
        .reservas-table tr:nth-child(even) td { background: #fafafa; }

        .num { color: #e63946; font-weight: 800; font-size: 9px; }
        .estado-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 2px;
            font-size: 7px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .estado-confirmada { background: #d4edda; color: #155724; }
        .estado-cancelada  { background: #f8d7da; color: #721c24; }
        .estado-completada { background: #cce5ff; color: #004085; }
        .estado-vencida    { background: #e2e3e5; color: #383d41; }

        .pasajeros-sub { margin-top: 3px; }
        .pasajero-item {
            font-size: 8px;
            color: #555;
            padding: 1px 0;
            border-bottom: 1px dotted #eee;
        }
        .pasajero-item:last-child { border-bottom: none; }
        .chk-si   { color: #155724; font-weight: 800; }
        .chk-no   { color: #721c24; font-weight: 800; }
        .chk-pend { color: #aaa; font-weight: 800; }

        .embarque-resumen { font-size: 9px; font-weight: 800; }
        .embarque-ok    { color: #155724; }
        .embarque-parcial { color: #856404; }
        .embarque-pend  { color: #aaa; font-weight: 600; }

        .totales {
            padding: 12px 20px;
            background: #f8f8f8;
            border-top: 2px solid #1a1a2e;
            margin-top: 12px;
        }
        .totales table { width: 100%; border-collapse: collapse; }
        .totales td { padding: 4px 8px; font-size: 10px; }
        .totales .t-label { color: #888; }
        .totales .t-value { font-weight: 800; color: #1a1a2e; text-align: right; }
        .totales .t-value-red { font-weight: 800; color: #e63946; text-align: right; font-size: 14px; }

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

        .page-break { page-break-after: always; }
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
                    <h1>Manifiesto de Embarque</h1>
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
                    <div class="label">Fecha de embarque</div>
                    <div class="value">{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</div>
                </td>
                <td>
                    <div class="label">Embarcación</div>
                    <div class="value">{{ $embarcacion ?? 'Todas' }}</div>
                </td>
                <td>
                    <div class="label">Total reservas</div>
                    <div class="value-red">{{ $reservas->count() }}</div>
                </td>
                <td>
                    <div class="label">Total pasajeros</div>
                    <div class="value-red">{{ $reservas->sum('total_personas') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Detalle de Reservas y Pasajeros</div>

    <table class="reservas-table">
        <thead>
            <tr>
                <th style="width:55px;">N° Reserva</th>
                <th style="width:80px;">Embarcación</th>
                <th style="width:70px;">Horario</th>
                <th style="width:100px;">Titular</th>
                <th style="width:70px;">Cédula</th>
                <th>Pasajeros</th>
                <th style="width:30px; text-align:center;">Pax</th>
                <th style="width:55px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservas as $reserva)
            <tr>
                <td class="num">#{{ str_pad($reserva->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $reserva->embarcacion->nombre }}</td>
                <td>
                    @if($reserva->hora_inicio)
                        {{ substr($reserva->hora_inicio, 0, 5) }} — {{ substr($reserva->hora_fin, 0, 5) }}
                    @else
                        —
                    @endif
                </td>
                <td>{{ $reserva->user->name }}</td>
                <td style="font-family: monospace; font-size:9px;">{{ $reserva->user->cedula }}</td>
                <td>
                    <div class="pasajeros-sub">
                        @foreach($reserva->pasajeros as $p)
                        <div class="pasajero-item">
                            @if($reserva->boleto?->estado === 'usado')
                                @if($p->presente)
                                    <span class="chk-si">[OK]</span>
                                @else
                                    <span class="chk-no">[X]</span>
                                @endif
                            @else
                                <span class="chk-pend">&bull;</span>
                            @endif
                            <strong>{{ $p->nombre }}</strong>
                            <span style="color:#aaa;"> · {{ $p->cedula }}</span>
                            @if($p->tipo)
                                <span style="color:#aaa;"> · {{ ucfirst($p->tipo) }}</span>
                            @endif
                            @if($p->carrera)
                                <span style="color:#aaa;"> · {{ $p->carrera }}</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </td>
                <td style="text-align:center; font-weight:800; font-size:12px; color:#e63946;">
                    {{ $reserva->total_personas }}
                    @if($reserva->boleto?->estado === 'usado')
                        @php $presentes = $reserva->pasajeros->where('presente', true)->count(); @endphp
                        <div class="embarque-resumen {{ $presentes === $reserva->total_personas ? 'embarque-ok' : 'embarque-parcial' }}">
                            {{ $presentes }}/{{ $reserva->total_personas }}
                        </div>
                    @else
                        <div class="embarque-resumen embarque-pend">pendiente</div>
                    @endif
                </td>
                <td>
                    <span class="estado-badge estado-{{ $reserva->estado }}">
                        {{ $reserva->estado }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $reservasEmbarcadas = $reservas->filter(fn($r) => $r->boleto?->estado === 'usado');
        $totalEmbarcaron    = $reservasEmbarcadas->sum(fn($r) => $r->pasajeros->where('presente', true)->count());
        $totalAusentes      = $reservasEmbarcadas->sum(fn($r) => $r->pasajeros->where('presente', false)->count());
        $porTipo = $reservas->where('estado', 'confirmada')
            ->flatMap(fn($r) => $r->pasajeros)
            ->groupBy(fn($p) => $p->tipo ?? 'externo')
            ->map->count();
    @endphp
    <div class="totales">
        <table>
            <tr>
                <td class="t-label">Total de reservas confirmadas</td>
                <td class="t-value">{{ $reservas->where('estado', 'confirmada')->count() }}</td>
            </tr>
            <tr>
                <td class="t-label">Total de pasajeros confirmados</td>
                <td class="t-value-red">{{ $reservas->where('estado', 'confirmada')->sum('total_personas') }}</td>
            </tr>
            <tr>
                <td class="t-label">Total de pasajeros que abordaron</td>
                <td class="t-value">{{ $totalEmbarcaron }}</td>
            </tr>
            <tr>
                <td class="t-label">Total de pasajeros ausentes</td>
                <td class="t-value-red">{{ $totalAusentes }}</td>
            </tr>
            @foreach(['estudiante' => 'Estudiantes', 'docente' => 'Docentes', 'administrativo' => 'Administrativos', 'externo' => 'Externos'] as $tipoKey => $tipoLabel)
                @if($porTipo->get($tipoKey))
                <tr>
                    <td class="t-label">Pasajeros confirmados · {{ $tipoLabel }}</td>
                    <td class="t-value">{{ $porTipo->get($tipoKey) }}</td>
                </tr>
                @endif
            @endforeach
        </table>
    </div>

    <div class="footer">
        ULEAM — Facultad de Ciencias de la Vida y Tecnologías &bull;
        Manifiesto generado electrónicamente &bull;
        {{ date('Y') }}
    </div>

</body>
</html>