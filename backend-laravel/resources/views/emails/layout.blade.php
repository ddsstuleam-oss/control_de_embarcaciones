<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <meta name="supported-color-schemes" content="light dark">
  <title>@yield('title', $appName ?? 'ULEAM APP')</title>
  <style>
    body{margin:0;background:#f1f5f9;font-family:Segoe UI,Arial,Helvetica,sans-serif;color:#334155}
    a{color:#2563eb;text-decoration:none}

    .bg-wrapper{background:#f1f5f9}
    .bg-card{background:#ffffff}
    .bg-header{background:#f8fafc;border-bottom:1px solid #e2e8f0}
    .bg-footer{background:#f8fafc;border-top:1px solid #e2e8f0;color:#94a3b8}

    .app-name{color:#0f172a}
    .heading{color:#0f172a}
    .accent{color:#2563eb}
    .text-body{color:#334155}
    .text-strong{color:#0f172a}
    .text-muted{color:#64748b}
    .divider{border-bottom:1px solid #e2e8f0}

    .hl{background:#fde68a;color:#78350f;padding:0 4px;border-radius:3px}
    .token{font-family:Consolas,Menlo,monospace;font-weight:700;letter-spacing:2px;background:#eff6ff;border:1px dashed #93c5fd;color:#1d4ed8;border-radius:12px;padding:14px 18px;font-size:18px;display:inline-block}
    .badge{display:inline-block;background:#16a34a;color:#fff;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:700}

    .info-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:6px 18px}
    .info-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #e2e8f0;font-size:13px}
    .info-row:last-child{border-bottom:none}
    .info-label{color:#64748b}
    .info-value{font-weight:700;color:#0f172a;text-align:right}

    @media (prefers-color-scheme: dark){
      body{background:#0b0f17 !important;color:#cbd5e1 !important}
      a{color:#60a5fa !important}

      .bg-wrapper{background:#0b0f17 !important}
      .bg-card{background:#111827 !important}
      .bg-header{background:#0b0f17 !important;border-bottom-color:#1f2937 !important}
      .bg-footer{background:#0b0f17 !important;border-top-color:#1f2937 !important;color:#64748b !important}

      .app-name{color:#e5e7eb !important}
      .heading{color:#f3f4f6 !important}
      .accent{color:#60a5fa !important}
      .text-body{color:#cbd5e1 !important}
      .text-strong{color:#ffffff !important}
      .text-muted{color:#9ca3af !important}
      .divider{border-bottom-color:#1f2937 !important}

      .hl{background:#fff176 !important;color:#111827 !important}
      .token{background:#1f2937 !important;border-color:#374151 !important;color:#93c5fd !important}

      .info-box{background:#1f2937 !important;border-color:#374151 !important}
      .info-row{border-bottom-color:#374151 !important}
      .info-label{color:#9ca3af !important}
      .info-value{color:#f3f4f6 !important}
    }
  </style>
</head>
<body>
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" class="bg-wrapper" style="background:#f1f5f9">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table width="600" cellpadding="0" cellspacing="0" role="presentation" class="bg-card" style="width:600px;max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;">
          <!-- Header -->
          <tr>
            <td class="bg-header" style="background:#f8fafc;padding:16px 20px;">
              <table width="100%" role="presentation">
                <tr>
                  <td align="left" style="vertical-align:middle;">
                    <table cellpadding="0" cellspacing="0" role="presentation"><tr>
                      <td bgcolor="#ffffff" style="background:#ffffff;border-radius:8px;padding:6px 10px;line-height:0;">
                        <img src="cid:logo" alt="ULEAM" height="36" style="display:block;border:0;outline:none;">
                      </td>
                    </tr></table>
                  </td>
                  <td align="right" class="app-name" style="vertical-align:middle;font-weight:700;color:#0f172a;font-size:14px;">
                    {{ $appName ?? 'ULEAM APP' }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Banner -->
          <tr>
            <td>
              <img src="cid:banner" alt="Banner ULEAM" width="600" style="display:block;width:100%;height:auto;">
            </td>
          </tr>

          <!-- Título -->
          <tr>
            <td style="padding:22px 24px 8px;">
              <div class="heading" style="font-size:22px;line-height:1.25;color:#0f172a;font-weight:800;">
                @yield('heading')
              </div>
            </td>
          </tr>

          <!-- Cuerpo -->
          @yield('content')

          <!-- Footer -->
          <tr>
            <td align="center" class="bg-footer" style="background:#f8fafc;color:#94a3b8;font-size:12px;padding:16px 24px;">
              © {{ date('Y') }} {{ $appName ?? 'ULEAM APP' }} — Universidad Laica Eloy Alfaro de Manabí
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
