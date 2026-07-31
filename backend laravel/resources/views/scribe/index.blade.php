<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>API — Control de Embarcaciones FCVT ULEAM</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
                    body .content .php-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.9.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.9.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;php&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
            <img src="images/logo-uleam-nuevo.png" alt="logo" class="logo" style="padding-top: 10px;" width="100%"/>
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                                    <ul id="tocify-subheader-introduction" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="autenticacion">
                                <a href="#autenticacion">Autenticación</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="roles-disponibles">
                                <a href="#roles-disponibles">Roles disponibles</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-autenticacion" class="tocify-header">
                <li class="tocify-item level-1" data-unique="autenticacion">
                    <a href="#autenticacion">Autenticación</a>
                </li>
                                    <ul id="tocify-subheader-autenticacion" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-register">
                                <a href="#autenticacion-POSTapi-register">Registro</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-login">
                                <a href="#autenticacion-POSTapi-login">Login</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-logout">
                                <a href="#autenticacion-POSTapi-logout">Logout</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-POSTapi-update-password">
                                <a href="#autenticacion-POSTapi-update-password">Actualizar contraseña</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacion-GETapi-me">
                                <a href="#autenticacion-GETapi-me">Usuario autenticado</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-recuperacion-de-contrasena" class="tocify-header">
                <li class="tocify-item level-1" data-unique="recuperacion-de-contrasena">
                    <a href="#recuperacion-de-contrasena">Recuperación de contraseña</a>
                </li>
                                    <ul id="tocify-subheader-recuperacion-de-contrasena" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="recuperacion-de-contrasena-POSTapi-forgot-password">
                                <a href="#recuperacion-de-contrasena-POSTapi-forgot-password">Solicitar recuperación de contraseña</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="recuperacion-de-contrasena-POSTapi-reset-password">
                                <a href="#recuperacion-de-contrasena-POSTapi-reset-password">Restablecer contraseña</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-perfil" class="tocify-header">
                <li class="tocify-item level-1" data-unique="perfil">
                    <a href="#perfil">Perfil</a>
                </li>
                                    <ul id="tocify-subheader-perfil" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="perfil-GETapi-perfil">
                                <a href="#perfil-GETapi-perfil">Ver mi perfil</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="perfil-PUTapi-perfil">
                                <a href="#perfil-PUTapi-perfil">Actualizar perfil</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="perfil-GETapi-perfil-reservas">
                                <a href="#perfil-GETapi-perfil-reservas">Historial de reservas</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="perfil-GETapi-perfil-actividades">
                                <a href="#perfil-GETapi-perfil-actividades">Historial de actividad</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-embarcaciones" class="tocify-header">
                <li class="tocify-item level-1" data-unique="embarcaciones">
                    <a href="#embarcaciones">Embarcaciones</a>
                </li>
                                    <ul id="tocify-subheader-embarcaciones" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="embarcaciones-GETapi-embarcaciones">
                                <a href="#embarcaciones-GETapi-embarcaciones">Listar embarcaciones</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="embarcaciones-GETapi-disponibilidad">
                                <a href="#embarcaciones-GETapi-disponibilidad">Disponibilidad por fecha</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="embarcaciones-GETapi-embarcaciones--id-">
                                <a href="#embarcaciones-GETapi-embarcaciones--id-">Ver embarcación</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="embarcaciones-POSTapi-admin-embarcaciones">
                                <a href="#embarcaciones-POSTapi-admin-embarcaciones">Crear embarcación</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="embarcaciones-PUTapi-admin-embarcaciones--id-">
                                <a href="#embarcaciones-PUTapi-admin-embarcaciones--id-">Actualizar embarcación</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="embarcaciones-DELETEapi-admin-embarcaciones--id-">
                                <a href="#embarcaciones-DELETEapi-admin-embarcaciones--id-">Eliminar embarcación</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-reservas" class="tocify-header">
                <li class="tocify-item level-1" data-unique="reservas">
                    <a href="#reservas">Reservas</a>
                </li>
                                    <ul id="tocify-subheader-reservas" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="reservas-GETapi-reservas">
                                <a href="#reservas-GETapi-reservas">Listar mis reservas</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-POSTapi-reservas">
                                <a href="#reservas-POSTapi-reservas">Crear reserva</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-GETapi-reservas--id-">
                                <a href="#reservas-GETapi-reservas--id-">Ver una reserva</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-GETapi-reservas-fecha">
                                <a href="#reservas-GETapi-reservas-fecha">Reservas por fecha</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-PATCHapi-reservas--id--cancelar">
                                <a href="#reservas-PATCHapi-reservas--id--cancelar">Cancelar reserva</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-GETapi-reservas-hoy">
                                <a href="#reservas-GETapi-reservas-hoy">Reservas de hoy (Operador)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-GETapi-admin-reservas">
                                <a href="#reservas-GETapi-admin-reservas">Listar todas las reservas (Admin)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-PATCHapi-admin-reservas--id--cancelar">
                                <a href="#reservas-PATCHapi-admin-reservas--id--cancelar">Cancelar reserva</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-DELETEapi-admin-reservas--id-">
                                <a href="#reservas-DELETEapi-admin-reservas--id-">Eliminar reserva (Admin)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="reservas-PATCHapi-admin-reservas--id--restore">
                                <a href="#reservas-PATCHapi-admin-reservas--id--restore">Restaurar reserva (Admin)</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-boletos" class="tocify-header">
                <li class="tocify-item level-1" data-unique="boletos">
                    <a href="#boletos">Boletos</a>
                </li>
                                    <ul id="tocify-subheader-boletos" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="boletos-POSTapi-validar-boleto--codigo-">
                                <a href="#boletos-POSTapi-validar-boleto--codigo-">Validar boleto QR</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="boletos-GETapi-boletos--id-">
                                <a href="#boletos-GETapi-boletos--id-">Ver boleto</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="boletos-GETapi-boletos--id--pdf">
                                <a href="#boletos-GETapi-boletos--id--pdf">Descargar PDF del boleto</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-actividad" class="tocify-header">
                <li class="tocify-item level-1" data-unique="actividad">
                    <a href="#actividad">Actividad</a>
                </li>
                                    <ul id="tocify-subheader-actividad" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="actividad-POSTapi-actividad">
                                <a href="#actividad-POSTapi-actividad">Registrar actividad</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-admin-dashboard" class="tocify-header">
                <li class="tocify-item level-1" data-unique="admin-dashboard">
                    <a href="#admin-dashboard">Admin — Dashboard</a>
                </li>
                                    <ul id="tocify-subheader-admin-dashboard" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="admin-dashboard-GETapi-admin-dashboard">
                                <a href="#admin-dashboard-GETapi-admin-dashboard">Resumen general</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-dashboard-GETapi-admin-estadisticas">
                                <a href="#admin-dashboard-GETapi-admin-estadisticas">Estadísticas por rango de fechas</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-admin-usuarios" class="tocify-header">
                <li class="tocify-item level-1" data-unique="admin-usuarios">
                    <a href="#admin-usuarios">Admin — Usuarios</a>
                </li>
                                    <ul id="tocify-subheader-admin-usuarios" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="admin-usuarios-GETapi-admin-usuarios">
                                <a href="#admin-usuarios-GETapi-admin-usuarios">Listar usuarios</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-usuarios-POSTapi-admin-usuarios">
                                <a href="#admin-usuarios-POSTapi-admin-usuarios">Crear usuario</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-usuarios-GETapi-admin-usuarios--id-">
                                <a href="#admin-usuarios-GETapi-admin-usuarios--id-">Ver usuario</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-usuarios-PATCHapi-admin-usuarios--id--toggle">
                                <a href="#admin-usuarios-PATCHapi-admin-usuarios--id--toggle">Activar / Desactivar usuario</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-usuarios-PATCHapi-admin-usuarios--id--rol">
                                <a href="#admin-usuarios-PATCHapi-admin-usuarios--id--rol">Cambiar rol</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-usuarios-DELETEapi-admin-usuarios--id-">
                                <a href="#admin-usuarios-DELETEapi-admin-usuarios--id-">Eliminar usuario</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-admin-reportes" class="tocify-header">
                <li class="tocify-item level-1" data-unique="admin-reportes">
                    <a href="#admin-reportes">Admin — Reportes</a>
                </li>
                                    <ul id="tocify-subheader-admin-reportes" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="admin-reportes-GETapi-admin-reportes-excel-pasajeros">
                                <a href="#admin-reportes-GETapi-admin-reportes-excel-pasajeros">Excel — pasajeros por fecha</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-reportes-GETapi-admin-reportes-excel-reservas">
                                <a href="#admin-reportes-GETapi-admin-reportes-excel-reservas">Excel — reservas por rango de fechas</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-reportes-GETapi-admin-reportes-pdf-manifiesto">
                                <a href="#admin-reportes-GETapi-admin-reportes-pdf-manifiesto">PDF — manifiesto de embarque</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-user">
                                <a href="#endpoints-GETapi-user">GET api/user</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Actualizado: 14/04/2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>Documentación oficial de la API REST del sistema institucional de control y reservas de embarcaciones de la Facultad de Ciencias de la Vida y Tecnologías — Universidad Laica Eloy Alfaro de Manabí.</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>
<p>Esta documentación describe todos los endpoints disponibles en el sistema de control de embarcaciones FCVT-ULEAM.</p>
<h2 id="autenticacion">Autenticación</h2>
<p>El sistema usa <strong>Laravel Sanctum</strong> con tokens Bearer. Para autenticarte:</p>
<ol>
<li>Haz <code>POST /api/login</code> con tu cédula y contraseña</li>
<li>Copia el <code>token</code> de la respuesta</li>
<li>Úsalo en el header: <code>Authorization: Bearer {token}</code></li>
</ol>
<h2 id="roles-disponibles">Roles disponibles</h2>
<ul>
<li><strong>admin</strong> — acceso total al sistema</li>
<li><strong>operador</strong> — validación de boletos en puerto</li>
<li><strong>usuario</strong> — reservas y boletos propios</li>
</ul>
<aside>Los ejemplos de código están disponibles en bash, javascript y php en el panel derecho.</aside>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {TU_TOKEN_AQUI}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>Obtén el token haciendo <b>POST /api/login</b> con tu cédula y contraseña. El token tiene una sola sesión activa a la vez.</p>

        <h1 id="autenticacion">Autenticación</h1>

    <p>Endpoints para registro, login, logout y gestión de contraseña.
El sistema usa Laravel Sanctum con tokens Bearer.</p>

                                <h2 id="autenticacion-POSTapi-register">Registro</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Registra un nuevo usuario en el sistema con rol <code>usuario</code> por defecto.
La cédula debe ser ecuatoriana válida (algoritmo del Registro Civil).</p>

<span id="example-requests-POSTapi-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/register" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"cedula\": \"1300000099\",
    \"name\": \"Juan Carlos Pérez\",
    \"email\": \"juan@uleam.edu.ec\",
    \"password\": \"Password123!\",
    \"password_confirmation\": \"Password123!\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/register"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "cedula": "1300000099",
    "name": "Juan Carlos Pérez",
    "email": "juan@uleam.edu.ec",
    "password": "Password123!",
    "password_confirmation": "Password123!"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/register';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'cedula' =&gt; '1300000099',
            'name' =&gt; 'Juan Carlos Pérez',
            'email' =&gt; 'juan@uleam.edu.ec',
            'password' =&gt; 'Password123!',
            'password_confirmation' =&gt; 'Password123!',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-register">
            <blockquote>
            <p>Example response (201, Registro exitoso):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Registro exitoso&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 4,
        &quot;cedula&quot;: &quot;1300000099&quot;,
        &quot;nombre&quot;: &quot;Juan Carlos P&eacute;rez&quot;,
        &quot;email&quot;: &quot;juan@uleam.edu.ec&quot;,
        &quot;activo&quot;: true,
        &quot;rol&quot;: &quot;usuario&quot;,
        &quot;dias_para_vencer&quot;: 90,
        &quot;miembro_desde&quot;: &quot;13/04/2026&quot;
    },
    &quot;token&quot;: &quot;4|abc123xyz...&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Cédula inválida):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;cedula&quot;: [
            &quot;La c&eacute;dula ecuatoriana no es v&aacute;lida.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Email duplicado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-register" data-method="POST"
      data-path="api/register"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-register"
                    onclick="tryItOut('POSTapi-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-register"
                    onclick="cancelTryOut('POSTapi-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-register"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cedula</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cedula"                data-endpoint="POSTapi-register"
               value="1300000099"
               data-component="body">
    <br>
<p>Cédula ecuatoriana válida de 10 dígitos. Example: <code>1300000099</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-register"
               value="Juan Carlos Pérez"
               data-component="body">
    <br>
<p>Nombre completo. Example: <code>Juan Carlos Pérez</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-register"
               value="juan@uleam.edu.ec"
               data-component="body">
    <br>
<p>Correo electrónico único. Example: <code>juan@uleam.edu.ec</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-register"
               value="Password123!"
               data-component="body">
    <br>
<p>Contraseña mínimo 8 caracteres. Example: <code>Password123!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-register"
               value="Password123!"
               data-component="body">
    <br>
<p>Confirmación de contraseña. Example: <code>Password123!</code></p>
        </div>
        </form>

                    <h2 id="autenticacion-POSTapi-login">Login</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Inicia sesión con cédula y contraseña. Retorna el token Bearer para usar en endpoints protegidos.
Invalida tokens anteriores — solo una sesión activa a la vez.
Verifica que la cuenta esté activa y que la contraseña no haya expirado (90 días).</p>

<span id="example-requests-POSTapi-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/login" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"cedula\": \"1300000001\",
    \"password\": \"Admin1234!\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/login"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "cedula": "1300000001",
    "password": "Admin1234!"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/login';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'cedula' =&gt; '1300000001',
            'password' =&gt; 'Admin1234!',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-login">
            <blockquote>
            <p>Example response (200, Login exitoso):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Inicio de sesi&oacute;n exitoso&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 1,
        &quot;cedula&quot;: &quot;1300000001&quot;,
        &quot;nombre&quot;: &quot;Administrador ULEAM&quot;,
        &quot;email&quot;: &quot;admin@uleam.edu.ec&quot;,
        &quot;activo&quot;: true,
        &quot;rol&quot;: &quot;admin&quot;,
        &quot;dias_para_vencer&quot;: 85
    },
    &quot;token&quot;: &quot;1|GwT1eMcqABR5AeCiUgmNOBSJZlcOvZlzm2Kr1fVYd7c9feaa&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;C&eacute;dula o contrase&ntilde;a incorrecta&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (403, Cuenta desactivada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Tu cuenta est&aacute; desactivada. Contacta al administrador.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (403, Contraseña expirada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Tu contrase&ntilde;a ha expirado. Debes cambiarla.&quot;,
    &quot;require_password_change&quot;: true,
    &quot;dias_sin_cambiar&quot;: 95
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-login" data-method="POST"
      data-path="api/login"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-login"
                    onclick="tryItOut('POSTapi-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-login"
                    onclick="cancelTryOut('POSTapi-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-login"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cedula</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cedula"                data-endpoint="POSTapi-login"
               value="1300000001"
               data-component="body">
    <br>
<p>Cédula ecuatoriana. Example: <code>1300000001</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-login"
               value="Admin1234!"
               data-component="body">
    <br>
<p>Contraseña. Example: <code>Admin1234!</code></p>
        </div>
        </form>

                    <h2 id="autenticacion-POSTapi-logout">Logout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Cierra la sesión actual invalidando el token Bearer activo.</p>

<span id="example-requests-POSTapi-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/logout" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/logout"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/logout';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-logout">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Sesi&oacute;n cerrada correctamente&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-logout" data-method="POST"
      data-path="api/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-logout"
                    onclick="tryItOut('POSTapi-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-logout"
                    onclick="cancelTryOut('POSTapi-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-logout"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="autenticacion-POSTapi-update-password">Actualizar contraseña</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Cambia la contraseña del usuario autenticado.
Invalida todos los tokens anteriores y retorna un nuevo token.
La nueva contraseña no puede ser igual a la actual.</p>

<span id="example-requests-POSTapi-update-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/update-password" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"current_password\": \"Admin1234!\",
    \"new_password\": \"NuevoPass123!\",
    \"new_password_confirmation\": \"NuevoPass123!\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/update-password"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "current_password": "Admin1234!",
    "new_password": "NuevoPass123!",
    "new_password_confirmation": "NuevoPass123!"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/update-password';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'current_password' =&gt; 'Admin1234!',
            'new_password' =&gt; 'NuevoPass123!',
            'new_password_confirmation' =&gt; 'NuevoPass123!',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-update-password">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Contrase&ntilde;a actualizada correctamente&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 1,
        &quot;cedula&quot;: &quot;1300000001&quot;,
        &quot;nombre&quot;: &quot;Administrador ULEAM&quot;
    },
    &quot;token&quot;: &quot;5|newtoken123...&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (403):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;La contrase&ntilde;a actual no es correcta&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Misma contraseña):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;new_password&quot;: [
            &quot;The new password and current password must be different.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-update-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-update-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-update-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-update-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-update-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-update-password" data-method="POST"
      data-path="api/update-password"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-update-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-update-password"
                    onclick="tryItOut('POSTapi-update-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-update-password"
                    onclick="cancelTryOut('POSTapi-update-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-update-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/update-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-update-password"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-update-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-update-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>current_password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="current_password"                data-endpoint="POSTapi-update-password"
               value="Admin1234!"
               data-component="body">
    <br>
<p>Contraseña actual. Example: <code>Admin1234!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>new_password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="new_password"                data-endpoint="POSTapi-update-password"
               value="NuevoPass123!"
               data-component="body">
    <br>
<p>Nueva contraseña mínimo 8 caracteres. Example: <code>NuevoPass123!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>new_password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="new_password_confirmation"                data-endpoint="POSTapi-update-password"
               value="NuevoPass123!"
               data-component="body">
    <br>
<p>Confirmación de nueva contraseña. Example: <code>NuevoPass123!</code></p>
        </div>
        </form>

                    <h2 id="autenticacion-GETapi-me">Usuario autenticado</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna los datos completos del usuario con sesión activa.
Útil para Flutter al iniciar la app y verificar el estado de la sesión.</p>

<span id="example-requests-GETapi-me">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/me" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/me"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/me';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-me">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;user&quot;: {
        &quot;id&quot;: 1,
        &quot;cedula&quot;: &quot;1300000001&quot;,
        &quot;nombre&quot;: &quot;Administrador ULEAM&quot;,
        &quot;email&quot;: &quot;admin@uleam.edu.ec&quot;,
        &quot;activo&quot;: true,
        &quot;rol&quot;: &quot;admin&quot;,
        &quot;dias_para_vencer&quot;: 85,
        &quot;miembro_desde&quot;: &quot;09/04/2026&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No autenticado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-me" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-me"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-me"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-me" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-me">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-me" data-method="GET"
      data-path="api/me"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-me', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-me"
                    onclick="tryItOut('GETapi-me');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-me"
                    onclick="cancelTryOut('GETapi-me');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-me"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/me</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-me"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="recuperacion-de-contrasena">Recuperación de contraseña</h1>

    <p>Endpoints para solicitar y restablecer contraseña olvidada.
El flujo es: solicitar token por email → recibir código de 6 dígitos → restablecer contraseña.
El token expira en 60 minutos.</p>

                                <h2 id="recuperacion-de-contrasena-POSTapi-forgot-password">Solicitar recuperación de contraseña</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Envía un código de 6 dígitos al correo registrado para restablecer la contraseña.
El código expira en 60 minutos.
Si ya existe un token anterior para ese email, se elimina y se genera uno nuevo.</p>

<span id="example-requests-POSTapi-forgot-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/forgot-password" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"estudiante@uleam.edu.ec\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/forgot-password"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "estudiante@uleam.edu.ec"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/forgot-password';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'email' =&gt; 'estudiante@uleam.edu.ec',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-forgot-password">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Correo de recuperaci&oacute;n enviado.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;El correo no est&aacute; registrado.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-forgot-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-forgot-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-forgot-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-forgot-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-forgot-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-forgot-password" data-method="POST"
      data-path="api/forgot-password"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-forgot-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-forgot-password"
                    onclick="tryItOut('POSTapi-forgot-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-forgot-password"
                    onclick="cancelTryOut('POSTapi-forgot-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-forgot-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/forgot-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-forgot-password"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-forgot-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-forgot-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-forgot-password"
               value="estudiante@uleam.edu.ec"
               data-component="body">
    <br>
<p>Correo electrónico registrado en el sistema. Example: <code>estudiante@uleam.edu.ec</code></p>
        </div>
        </form>

                    <h2 id="recuperacion-de-contrasena-POSTapi-reset-password">Restablecer contraseña</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Restablece la contraseña usando el código de 6 dígitos recibido por email.
El token es de un solo uso — se elimina al ser utilizado.
El token expira a los 60 minutos de haber sido generado.</p>

<span id="example-requests-POSTapi-reset-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/reset-password" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"estudiante@uleam.edu.ec\",
    \"token\": \"A3F9K2\",
    \"password\": \"NuevoPass123!\",
    \"password_confirmation\": \"NuevoPass123!\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reset-password"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "estudiante@uleam.edu.ec",
    "token": "A3F9K2",
    "password": "NuevoPass123!",
    "password_confirmation": "NuevoPass123!"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/reset-password';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'email' =&gt; 'estudiante@uleam.edu.ec',
            'token' =&gt; 'A3F9K2',
            'password' =&gt; 'NuevoPass123!',
            'password_confirmation' =&gt; 'NuevoPass123!',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-reset-password">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Contrase&ntilde;a actualizada correctamente.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Token inválido):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Token inv&aacute;lido.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Token expirado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;El token ha expirado.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Usuario no encontrado.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;password&quot;: [
            &quot;The password field confirmation does not match.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-reset-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-reset-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-reset-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-reset-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-reset-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-reset-password" data-method="POST"
      data-path="api/reset-password"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-reset-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-reset-password"
                    onclick="tryItOut('POSTapi-reset-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-reset-password"
                    onclick="cancelTryOut('POSTapi-reset-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-reset-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/reset-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-reset-password"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-reset-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-reset-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-reset-password"
               value="estudiante@uleam.edu.ec"
               data-component="body">
    <br>
<p>Correo electrónico registrado. Example: <code>estudiante@uleam.edu.ec</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>token</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="token"                data-endpoint="POSTapi-reset-password"
               value="A3F9K2"
               data-component="body">
    <br>
<p>Código de 6 dígitos recibido por email. Example: <code>A3F9K2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-reset-password"
               value="NuevoPass123!"
               data-component="body">
    <br>
<p>Nueva contraseña mínimo 6 caracteres. Example: <code>NuevoPass123!</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password_confirmation</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password_confirmation"                data-endpoint="POSTapi-reset-password"
               value="NuevoPass123!"
               data-component="body">
    <br>
<p>Confirmación de nueva contraseña. Example: <code>NuevoPass123!</code></p>
        </div>
        </form>

                <h1 id="perfil">Perfil</h1>

    <p>Endpoints para gestión del perfil del usuario autenticado.</p>

                                <h2 id="perfil-GETapi-perfil">Ver mi perfil</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna los datos del usuario autenticado junto con estadísticas de sus reservas.</p>

<span id="example-requests-GETapi-perfil">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/perfil" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/perfil"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/perfil';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-perfil">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;usuario&quot;: {
        &quot;id&quot;: 3,
        &quot;cedula&quot;: &quot;1300000003&quot;,
        &quot;nombre&quot;: &quot;Estudiante Test&quot;,
        &quot;email&quot;: &quot;estudiante@uleam.edu.ec&quot;,
        &quot;activo&quot;: true,
        &quot;rol&quot;: &quot;usuario&quot;,
        &quot;dias_para_vencer&quot;: 85,
        &quot;miembro_desde&quot;: &quot;09/04/2026&quot;
    },
    &quot;estadisticas&quot;: {
        &quot;total_reservas&quot;: 5,
        &quot;reservas_activas&quot;: 2,
        &quot;reservas_canceladas&quot;: 1
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-perfil" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-perfil"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-perfil"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-perfil" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-perfil">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-perfil" data-method="GET"
      data-path="api/perfil"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-perfil', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-perfil"
                    onclick="tryItOut('GETapi-perfil');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-perfil"
                    onclick="cancelTryOut('GETapi-perfil');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-perfil"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/perfil</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-perfil"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-perfil"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-perfil"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="perfil-PUTapi-perfil">Actualizar perfil</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Actualiza el nombre y/o email del usuario autenticado.</p>

<span id="example-requests-PUTapi-perfil">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/perfil" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"Juan Carlos Pérez\",
    \"email\": \"juan@uleam.edu.ec\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/perfil"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "Juan Carlos Pérez",
    "email": "juan@uleam.edu.ec"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/perfil';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'Juan Carlos Pérez',
            'email' =&gt; 'juan@uleam.edu.ec',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PUTapi-perfil">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Perfil actualizado correctamente&quot;,
    &quot;usuario&quot;: {
        &quot;id&quot;: 3,
        &quot;cedula&quot;: &quot;1300000003&quot;,
        &quot;nombre&quot;: &quot;Juan Carlos P&eacute;rez&quot;,
        &quot;email&quot;: &quot;juan@uleam.edu.ec&quot;,
        &quot;rol&quot;: &quot;usuario&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Email duplicado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-perfil" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-perfil"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-perfil"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-perfil" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-perfil">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-perfil" data-method="PUT"
      data-path="api/perfil"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-perfil', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-perfil"
                    onclick="tryItOut('PUTapi-perfil');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-perfil"
                    onclick="cancelTryOut('PUTapi-perfil');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-perfil"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/perfil</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-perfil"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-perfil"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-perfil"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-perfil"
               value="Juan Carlos Pérez"
               data-component="body">
    <br>
<p>Nombre completo del usuario. Example: <code>Juan Carlos Pérez</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-perfil"
               value="juan@uleam.edu.ec"
               data-component="body">
    <br>
<p>Correo electrónico único. Example: <code>juan@uleam.edu.ec</code></p>
        </div>
        </form>

                    <h2 id="perfil-GETapi-perfil-reservas">Historial de reservas</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna el historial paginado de reservas del usuario autenticado.</p>

<span id="example-requests-GETapi-perfil-reservas">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/perfil/reservas?per_page=5" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/perfil/reservas"
);

const params = {
    "per_page": "5",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/perfil/reservas';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'per_page' =&gt; '5',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-perfil-reservas">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;fecha&quot;: &quot;2026-05-10&quot;,
            &quot;total_personas&quot;: 2,
            &quot;estado&quot;: &quot;confirmada&quot;,
            &quot;embarcacion&quot;: {
                &quot;nombre&quot;: &quot;Lancha Uleam I&quot;
            },
            &quot;boleto&quot;: {
                &quot;codigo_qr&quot;: &quot;01KP2EZ650JKEFGPEZHGQWM5PD&quot;,
                &quot;estado&quot;: &quot;valido&quot;
            }
        }
    ],
    &quot;current_page&quot;: 1,
    &quot;total&quot;: 5,
    &quot;per_page&quot;: 10
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-perfil-reservas" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-perfil-reservas"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-perfil-reservas"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-perfil-reservas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-perfil-reservas">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-perfil-reservas" data-method="GET"
      data-path="api/perfil/reservas"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-perfil-reservas', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-perfil-reservas"
                    onclick="tryItOut('GETapi-perfil-reservas');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-perfil-reservas"
                    onclick="cancelTryOut('GETapi-perfil-reservas');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-perfil-reservas"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/perfil/reservas</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-perfil-reservas"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-perfil-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-perfil-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-perfil-reservas"
               value="5"
               data-component="query">
    <br>
<p>Resultados por página (default: 10). Example: <code>5</code></p>
            </div>
                </form>

                    <h2 id="perfil-GETapi-perfil-actividades">Historial de actividad</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna el historial paginado de actividades registradas del usuario autenticado.</p>

<span id="example-requests-GETapi-perfil-actividades">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/perfil/actividades?per_page=10" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/perfil/actividades"
);

const params = {
    "per_page": "10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/perfil/actividades';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'per_page' =&gt; '10',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-perfil-actividades">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;accion&quot;: &quot;login&quot;,
            &quot;descripcion&quot;: &quot;Inicio de sesi&oacute;n exitoso&quot;,
            &quot;ip&quot;: &quot;127.0.0.1&quot;,
            &quot;dispositivo&quot;: &quot;Android&quot;,
            &quot;fecha&quot;: &quot;13/04/2026 21:55&quot;
        }
    ],
    &quot;current_page&quot;: 1,
    &quot;total&quot;: 10,
    &quot;per_page&quot;: 20
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-perfil-actividades" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-perfil-actividades"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-perfil-actividades"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-perfil-actividades" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-perfil-actividades">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-perfil-actividades" data-method="GET"
      data-path="api/perfil/actividades"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-perfil-actividades', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-perfil-actividades"
                    onclick="tryItOut('GETapi-perfil-actividades');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-perfil-actividades"
                    onclick="cancelTryOut('GETapi-perfil-actividades');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-perfil-actividades"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/perfil/actividades</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-perfil-actividades"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-perfil-actividades"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-perfil-actividades"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-perfil-actividades"
               value="10"
               data-component="query">
    <br>
<p>Resultados por página (default: 20). Example: <code>10</code></p>
            </div>
                </form>

                <h1 id="embarcaciones">Embarcaciones</h1>

    <p>Endpoints para consulta y gestión de embarcaciones.
Los endpoints de listado y disponibilidad son públicos.
Los endpoints de creación, actualización y eliminación requieren rol admin.</p>

                                <h2 id="embarcaciones-GETapi-embarcaciones">Listar embarcaciones</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna todas las embarcaciones registradas ordenadas por nombre.
Endpoint público, no requiere autenticación.</p>

<span id="example-requests-GETapi-embarcaciones">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/embarcaciones" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/embarcaciones"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/embarcaciones';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-embarcaciones">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;nombre&quot;: &quot;Lancha Uleam I&quot;,
            &quot;capacidad&quot;: 25,
            &quot;estado&quot;: &quot;disponible&quot;,
            &quot;descripcion&quot;: &quot;Embarcaci&oacute;n principal para recorridos acad&eacute;micos&quot;,
            &quot;imagen_url&quot;: &quot;http://localhost:8000/storage/embarcaciones/lancha.jpg&quot;,
            &quot;creado_en&quot;: &quot;09/04/2026&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-embarcaciones" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-embarcaciones"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-embarcaciones"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-embarcaciones" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-embarcaciones">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-embarcaciones" data-method="GET"
      data-path="api/embarcaciones"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-embarcaciones', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-embarcaciones"
                    onclick="tryItOut('GETapi-embarcaciones');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-embarcaciones"
                    onclick="cancelTryOut('GETapi-embarcaciones');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-embarcaciones"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/embarcaciones</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-embarcaciones"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-embarcaciones"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-embarcaciones"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="embarcaciones-GETapi-disponibilidad">Disponibilidad por fecha</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna la disponibilidad de cupos de todas las embarcaciones activas para una fecha específica.
Endpoint público, no requiere autenticación.</p>

<span id="example-requests-GETapi-disponibilidad">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/disponibilidad?fecha=2026-05-10" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"fecha\": \"2107-05-14\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/disponibilidad"
);

const params = {
    "fecha": "2026-05-10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "fecha": "2107-05-14"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/disponibilidad';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'fecha' =&gt; '2026-05-10',
        ],
        'json' =&gt; [
            'fecha' =&gt; '2107-05-14',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-disponibilidad">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;fecha&quot;: &quot;2026-05-10&quot;,
    &quot;embarcaciones&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;nombre&quot;: &quot;Lancha Uleam I&quot;,
            &quot;capacidad&quot;: 25,
            &quot;imagen_url&quot;: null,
            &quot;reservados&quot;: 10,
            &quot;disponibles&quot;: 15,
            &quot;disponible&quot;: true
        }
    ]
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Fecha inválida):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;fecha&quot;: [
            &quot;The fecha field must be a date after or equal to today.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-disponibilidad" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-disponibilidad"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-disponibilidad"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-disponibilidad" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-disponibilidad">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-disponibilidad" data-method="GET"
      data-path="api/disponibilidad"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-disponibilidad', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-disponibilidad"
                    onclick="tryItOut('GETapi-disponibilidad');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-disponibilidad"
                    onclick="cancelTryOut('GETapi-disponibilidad');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-disponibilidad"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/disponibilidad</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-disponibilidad"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-disponibilidad"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-disponibilidad"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-disponibilidad"
               value="2026-05-10"
               data-component="query">
    <br>
<p>Fecha a consultar (YYYY-MM-DD), debe ser hoy o futura. Example: <code>2026-05-10</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-disponibilidad"
               value="2107-05-14"
               data-component="body">
    <br>
<p>validation.date validation.after_or_equal. Example: <code>2107-05-14</code></p>
        </div>
        </form>

                    <h2 id="embarcaciones-GETapi-embarcaciones--id-">Ver embarcación</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna el detalle de una embarcación específica.</p>

<span id="example-requests-GETapi-embarcaciones--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/embarcaciones/1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/embarcaciones/1"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/embarcaciones/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-embarcaciones--id-">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;nombre&quot;: &quot;Lancha Uleam I&quot;,
        &quot;capacidad&quot;: 25,
        &quot;estado&quot;: &quot;disponible&quot;,
        &quot;descripcion&quot;: &quot;Embarcaci&oacute;n principal para recorridos acad&eacute;micos&quot;,
        &quot;imagen_url&quot;: null,
        &quot;creado_en&quot;: &quot;09/04/2026&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-embarcaciones--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-embarcaciones--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-embarcaciones--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-embarcaciones--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-embarcaciones--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-embarcaciones--id-" data-method="GET"
      data-path="api/embarcaciones/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-embarcaciones--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-embarcaciones--id-"
                    onclick="tryItOut('GETapi-embarcaciones--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-embarcaciones--id-"
                    onclick="cancelTryOut('GETapi-embarcaciones--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-embarcaciones--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/embarcaciones/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-embarcaciones--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-embarcaciones--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-embarcaciones--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-embarcaciones--id-"
               value="1"
               data-component="url">
    <br>
<p>ID de la embarcación. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="embarcaciones-POSTapi-admin-embarcaciones">Crear embarcación</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Crea una nueva embarcación en el sistema. Requiere rol admin.
Soporta subida de imagen en formato multipart/form-data.</p>

<span id="example-requests-POSTapi-admin-embarcaciones">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/admin/embarcaciones" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "nombre=Lancha Uleam III"\
    --form "capacidad=30"\
    --form "estado=disponible"\
    --form "descripcion=Embarcación de transporte estudiantil"\
    --form "imagen=@C:\Users\jeanc\AppData\Local\Temp\phpDD9C.tmp" </code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/embarcaciones"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('nombre', 'Lancha Uleam III');
body.append('capacidad', '30');
body.append('estado', 'disponible');
body.append('descripcion', 'Embarcación de transporte estudiantil');
body.append('imagen', document.querySelector('input[name="imagen"]').files[0]);

fetch(url, {
    method: "POST",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/embarcaciones';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'multipart/form-data',
            'Accept' =&gt; 'application/json',
        ],
        'multipart' =&gt; [
            [
                'name' =&gt; 'nombre',
                'contents' =&gt; 'Lancha Uleam III'
            ],
            [
                'name' =&gt; 'capacidad',
                'contents' =&gt; '30'
            ],
            [
                'name' =&gt; 'estado',
                'contents' =&gt; 'disponible'
            ],
            [
                'name' =&gt; 'descripcion',
                'contents' =&gt; 'Embarcación de transporte estudiantil'
            ],
            [
                'name' =&gt; 'imagen',
                'contents' =&gt; fopen('C:\Users\jeanc\AppData\Local\Temp\phpDD9C.tmp', 'r')
            ],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-embarcaciones">
            <blockquote>
            <p>Example response (201, Creada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Embarcaci&oacute;n creada correctamente&quot;,
    &quot;embarcacion&quot;: {
        &quot;id&quot;: 5,
        &quot;nombre&quot;: &quot;Lancha Uleam III&quot;,
        &quot;capacidad&quot;: 30,
        &quot;estado&quot;: &quot;disponible&quot;,
        &quot;descripcion&quot;: &quot;Embarcaci&oacute;n de transporte estudiantil&quot;,
        &quot;imagen_url&quot;: null,
        &quot;creado_en&quot;: &quot;13/04/2026&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Nombre duplicado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;nombre&quot;: [
            &quot;The nombre has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-admin-embarcaciones" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-embarcaciones"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-embarcaciones"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-embarcaciones" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-embarcaciones">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-embarcaciones" data-method="POST"
      data-path="api/admin/embarcaciones"
      data-authed="1"
      data-hasfiles="1"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-embarcaciones', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-embarcaciones"
                    onclick="tryItOut('POSTapi-admin-embarcaciones');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-embarcaciones"
                    onclick="cancelTryOut('POSTapi-admin-embarcaciones');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-embarcaciones"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/embarcaciones</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-admin-embarcaciones"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-embarcaciones"
               value="multipart/form-data"
               data-component="header">
    <br>
<p>Example: <code>multipart/form-data</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-embarcaciones"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nombre</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="nombre"                data-endpoint="POSTapi-admin-embarcaciones"
               value="Lancha Uleam III"
               data-component="body">
    <br>
<p>Nombre único de la embarcación. Example: <code>Lancha Uleam III</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>capacidad</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="capacidad"                data-endpoint="POSTapi-admin-embarcaciones"
               value="30"
               data-component="body">
    <br>
<p>Capacidad máxima de pasajeros (1-500). Example: <code>30</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>estado</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="estado"                data-endpoint="POSTapi-admin-embarcaciones"
               value="disponible"
               data-component="body">
    <br>
<p>Estado inicial: disponible o mantenimiento (default: disponible). Example: <code>disponible</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>descripcion</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="descripcion"                data-endpoint="POSTapi-admin-embarcaciones"
               value="Embarcación de transporte estudiantil"
               data-component="body">
    <br>
<p>Descripción de la embarcación. Example: <code>Embarcación de transporte estudiantil</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>imagen</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="file" style="display: none"
                              name="imagen"                data-endpoint="POSTapi-admin-embarcaciones"
               value=""
               data-component="body">
    <br>
<p>Imagen de la embarcación (jpg, png, webp, max 2MB). Example: <code>C:\Users\jeanc\AppData\Local\Temp\phpDD9C.tmp</code></p>
        </div>
        </form>

                    <h2 id="embarcaciones-PUTapi-admin-embarcaciones--id-">Actualizar embarcación</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Actualiza los datos de una embarcación. Requiere rol admin.
No permite poner en mantenimiento si hay reservas futuras activas.</p>

<span id="example-requests-PUTapi-admin-embarcaciones--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/admin/embarcaciones/1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "nombre=Lancha Uleam I Renovada"\
    --form "capacidad=30"\
    --form "estado=mantenimiento"\
    --form "descripcion=Embarcación renovada 2026"\
    --form "imagen=@C:\Users\jeanc\AppData\Local\Temp\phpDDAE.tmp" </code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/embarcaciones/1"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('nombre', 'Lancha Uleam I Renovada');
body.append('capacidad', '30');
body.append('estado', 'mantenimiento');
body.append('descripcion', 'Embarcación renovada 2026');
body.append('imagen', document.querySelector('input[name="imagen"]').files[0]);

fetch(url, {
    method: "PUT",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/embarcaciones/1';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'multipart/form-data',
            'Accept' =&gt; 'application/json',
        ],
        'multipart' =&gt; [
            [
                'name' =&gt; 'nombre',
                'contents' =&gt; 'Lancha Uleam I Renovada'
            ],
            [
                'name' =&gt; 'capacidad',
                'contents' =&gt; '30'
            ],
            [
                'name' =&gt; 'estado',
                'contents' =&gt; 'mantenimiento'
            ],
            [
                'name' =&gt; 'descripcion',
                'contents' =&gt; 'Embarcación renovada 2026'
            ],
            [
                'name' =&gt; 'imagen',
                'contents' =&gt; fopen('C:\Users\jeanc\AppData\Local\Temp\phpDDAE.tmp', 'r')
            ],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PUTapi-admin-embarcaciones--id-">
            <blockquote>
            <p>Example response (200, Actualizada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Embarcaci&oacute;n actualizada correctamente&quot;,
    &quot;embarcacion&quot;: {
        &quot;id&quot;: 1,
        &quot;nombre&quot;: &quot;Lancha Uleam I Renovada&quot;,
        &quot;capacidad&quot;: 30,
        &quot;estado&quot;: &quot;disponible&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Reservas futuras):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No se puede poner en mantenimiento, hay reservas futuras activas&quot;,
    &quot;reservas_futuras&quot;: 3
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-admin-embarcaciones--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-admin-embarcaciones--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-admin-embarcaciones--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-admin-embarcaciones--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-admin-embarcaciones--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-admin-embarcaciones--id-" data-method="PUT"
      data-path="api/admin/embarcaciones/{id}"
      data-authed="1"
      data-hasfiles="1"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-admin-embarcaciones--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-admin-embarcaciones--id-"
                    onclick="tryItOut('PUTapi-admin-embarcaciones--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-admin-embarcaciones--id-"
                    onclick="cancelTryOut('PUTapi-admin-embarcaciones--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-admin-embarcaciones--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/admin/embarcaciones/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="multipart/form-data"
               data-component="header">
    <br>
<p>Example: <code>multipart/form-data</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="1"
               data-component="url">
    <br>
<p>ID de la embarcación. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>nombre</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="nombre"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="Lancha Uleam I Renovada"
               data-component="body">
    <br>
<p>Nuevo nombre único. Example: <code>Lancha Uleam I Renovada</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>capacidad</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="capacidad"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="30"
               data-component="body">
    <br>
<p>Nueva capacidad (1-500). Example: <code>30</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>estado</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="estado"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="mantenimiento"
               data-component="body">
    <br>
<p>Nuevo estado: disponible o mantenimiento. Example: <code>mantenimiento</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>descripcion</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="descripcion"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value="Embarcación renovada 2026"
               data-component="body">
    <br>
<p>Nueva descripción. Example: <code>Embarcación renovada 2026</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>imagen</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="file" style="display: none"
                              name="imagen"                data-endpoint="PUTapi-admin-embarcaciones--id-"
               value=""
               data-component="body">
    <br>
<p>Nueva imagen (jpg, png, webp, max 2MB). Example: <code>C:\Users\jeanc\AppData\Local\Temp\phpDDAE.tmp</code></p>
        </div>
        </form>

                    <h2 id="embarcaciones-DELETEapi-admin-embarcaciones--id-">Eliminar embarcación</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Elimina una embarcación del sistema. Requiere rol admin.
No permite eliminar si tiene reservas futuras activas.
Elimina también la imagen del storage si existe.</p>

<span id="example-requests-DELETEapi-admin-embarcaciones--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost:8000/api/admin/embarcaciones/1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/embarcaciones/1"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/embarcaciones/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-embarcaciones--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Embarcaci&oacute;n eliminada correctamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Reservas futuras):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No se puede eliminar, tiene reservas futuras activas&quot;,
    &quot;reservas_futuras&quot;: 2
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-admin-embarcaciones--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-embarcaciones--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-embarcaciones--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-embarcaciones--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-embarcaciones--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-embarcaciones--id-" data-method="DELETE"
      data-path="api/admin/embarcaciones/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-embarcaciones--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-embarcaciones--id-"
                    onclick="tryItOut('DELETEapi-admin-embarcaciones--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-embarcaciones--id-"
                    onclick="cancelTryOut('DELETEapi-admin-embarcaciones--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-embarcaciones--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/embarcaciones/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-admin-embarcaciones--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-embarcaciones--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-embarcaciones--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-admin-embarcaciones--id-"
               value="1"
               data-component="url">
    <br>
<p>ID de la embarcación. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="reservas">Reservas</h1>

    <p>Endpoints para gestión de reservas de embarcaciones.</p>

                                <h2 id="reservas-GETapi-reservas">Listar mis reservas</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna todas las reservas del usuario autenticado.</p>

<span id="example-requests-GETapi-reservas">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reservas" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reservas"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/reservas';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-reservas">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;fecha&quot;: &quot;2026-05-10&quot;,
            &quot;total_personas&quot;: 2,
            &quot;estado&quot;: &quot;confirmada&quot;,
            &quot;embarcacion&quot;: {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Lancha Uleam I&quot;
            },
            &quot;boleto&quot;: {
                &quot;id&quot;: 1,
                &quot;codigo_qr&quot;: &quot;01KP2EZ650JKEFGPEZHGQWM5PD&quot;,
                &quot;estado&quot;: &quot;valido&quot;
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reservas" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reservas"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reservas"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reservas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reservas">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reservas" data-method="GET"
      data-path="api/reservas"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reservas', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reservas"
                    onclick="tryItOut('GETapi-reservas');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reservas"
                    onclick="cancelTryOut('GETapi-reservas');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reservas"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reservas</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-reservas"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="reservas-POSTapi-reservas">Crear reserva</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Crea una nueva reserva con pasajeros y boleto QR. Envía email de confirmación con PDF adjunto.
Usa lock de base de datos para evitar condiciones de carrera en los cupos.</p>

<span id="example-requests-POSTapi-reservas">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/reservas" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"embarcacion_id\": 1,
    \"fecha\": \"2026-05-10\",
    \"total_personas\": 2,
    \"pasajeros\": [
        \"consequatur\"
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reservas"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "embarcacion_id": 1,
    "fecha": "2026-05-10",
    "total_personas": 2,
    "pasajeros": [
        "consequatur"
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/reservas';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'embarcacion_id' =&gt; 1,
            'fecha' =&gt; '2026-05-10',
            'total_personas' =&gt; 2,
            'pasajeros' =&gt; [
                'consequatur',
            ],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-reservas">
            <blockquote>
            <p>Example response (201, Reserva creada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Reserva creada exitosamente&quot;,
    &quot;reserva&quot;: {
        &quot;id&quot;: 1,
        &quot;fecha&quot;: &quot;2026-05-10&quot;,
        &quot;total_personas&quot;: 2,
        &quot;estado&quot;: &quot;confirmada&quot;,
        &quot;boleto&quot;: {
            &quot;codigo_qr&quot;: &quot;01KP2EZ650JKEFGPEZHGQWM5PD&quot;,
            &quot;estado&quot;: &quot;valido&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Sin cupos):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No hay cupos suficientes&quot;,
    &quot;disponibles&quot;: 3,
    &quot;solicitados&quot;: 5
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, No disponible):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Embarcaci&oacute;n no disponible&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;El n&uacute;mero de pasajeros no coincide con total_personas&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-reservas" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-reservas"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-reservas"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-reservas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-reservas">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-reservas" data-method="POST"
      data-path="api/reservas"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-reservas', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-reservas"
                    onclick="tryItOut('POSTapi-reservas');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-reservas"
                    onclick="cancelTryOut('POSTapi-reservas');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-reservas"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/reservas</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-reservas"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="embarcacion_id"                data-endpoint="POSTapi-reservas"
               value="1"
               data-component="body">
    <br>
<p>ID de la embarcación. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="POSTapi-reservas"
               value="2026-05-10"
               data-component="body">
    <br>
<p>Fecha del viaje (YYYY-MM-DD), debe ser hoy o futura. Example: <code>2026-05-10</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>total_personas</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="total_personas"                data-endpoint="POSTapi-reservas"
               value="2"
               data-component="body">
    <br>
<p>Número total de pasajeros (1-100). Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>pasajeros</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Lista de pasajeros, debe coincidir con total_personas.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>nombre</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pasajeros.0.nombre"                data-endpoint="POSTapi-reservas"
               value="Juan Pérez"
               data-component="body">
    <br>
<p>Nombre completo del pasajero. Example: <code>Juan Pérez</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>cedula</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pasajeros.0.cedula"                data-endpoint="POSTapi-reservas"
               value="0950638675"
               data-component="body">
    <br>
<p>Cédula del pasajero. Example: <code>0950638675</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>tipo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pasajeros.0.tipo"                data-endpoint="POSTapi-reservas"
               value="estudiante"
               data-component="body">
    <br>
<p>Tipo de pasajero: estudiante, docente, administrativo, externo. Example: <code>estudiante</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>carrera</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pasajeros.0.carrera"                data-endpoint="POSTapi-reservas"
               value="Medicina Veterinaria"
               data-component="body">
    <br>
<p>Carrera (solo estudiantes). Example: <code>Medicina Veterinaria</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>telefono</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pasajeros.0.telefono"                data-endpoint="POSTapi-reservas"
               value="0991234567"
               data-component="body">
    <br>
<p>Teléfono de contacto. Example: <code>0991234567</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pasajeros.0.email"                data-endpoint="POSTapi-reservas"
               value="juan@example.com"
               data-component="body">
    <br>
<p>Correo del pasajero. Example: <code>juan@example.com</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                    <h2 id="reservas-GETapi-reservas--id-">Ver una reserva</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna el detalle completo de una reserva del usuario autenticado.</p>

<span id="example-requests-GETapi-reservas--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reservas/1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reservas/1"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/reservas/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-reservas--id-">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;fecha&quot;: &quot;2026-05-10&quot;,
        &quot;total_personas&quot;: 2,
        &quot;estado&quot;: &quot;confirmada&quot;,
        &quot;embarcacion&quot;: {
            &quot;id&quot;: 1,
            &quot;nombre&quot;: &quot;Lancha Uleam I&quot;
        },
        &quot;pasajeros&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;nombre&quot;: &quot;Juan P&eacute;rez&quot;,
                &quot;cedula&quot;: &quot;0950638675&quot;,
                &quot;tipo&quot;: &quot;estudiante&quot;
            }
        ],
        &quot;boleto&quot;: {
            &quot;codigo_qr&quot;: &quot;01KP2EZ650JKEFGPEZHGQWM5PD&quot;,
            &quot;estado&quot;: &quot;valido&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reservas--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reservas--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reservas--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reservas--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reservas--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reservas--id-" data-method="GET"
      data-path="api/reservas/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reservas--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reservas--id-"
                    onclick="tryItOut('GETapi-reservas--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reservas--id-"
                    onclick="cancelTryOut('GETapi-reservas--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reservas--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reservas/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-reservas--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reservas--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reservas--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-reservas--id-"
               value="1"
               data-component="url">
    <br>
<p>ID de la reserva. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="reservas-GETapi-reservas-fecha">Reservas por fecha</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna las reservas del usuario autenticado filtradas por fecha.</p>

<span id="example-requests-GETapi-reservas-fecha">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reservas-fecha?fecha=2026-05-10" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"fecha\": \"2026-04-14T23:32:05\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reservas-fecha"
);

const params = {
    "fecha": "2026-05-10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "fecha": "2026-04-14T23:32:05"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/reservas-fecha';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'fecha' =&gt; '2026-05-10',
        ],
        'json' =&gt; [
            'fecha' =&gt; '2026-04-14T23:32:05',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-reservas-fecha">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;fecha&quot;: &quot;2026-05-10&quot;,
            &quot;estado&quot;: &quot;confirmada&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reservas-fecha" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reservas-fecha"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reservas-fecha"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reservas-fecha" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reservas-fecha">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reservas-fecha" data-method="GET"
      data-path="api/reservas-fecha"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reservas-fecha', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reservas-fecha"
                    onclick="tryItOut('GETapi-reservas-fecha');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reservas-fecha"
                    onclick="cancelTryOut('GETapi-reservas-fecha');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reservas-fecha"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reservas-fecha</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-reservas-fecha"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reservas-fecha"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reservas-fecha"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-reservas-fecha"
               value="2026-05-10"
               data-component="query">
    <br>
<p>Fecha a consultar (YYYY-MM-DD). Example: <code>2026-05-10</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-reservas-fecha"
               value="2026-04-14T23:32:05"
               data-component="body">
    <br>
<p>validation.date. Example: <code>2026-04-14T23:32:05</code></p>
        </div>
        </form>

                    <h2 id="reservas-PATCHapi-reservas--id--cancelar">Cancelar reserva</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Cancela una reserva del usuario autenticado. Solo se puede cancelar si la fecha es posterior a hoy.
Invalida automáticamente el boleto asociado.</p>

<span id="example-requests-PATCHapi-reservas--id--cancelar">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://localhost:8000/api/reservas/1/cancelar" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reservas/1/cancelar"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PATCH",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/reservas/1/cancelar';
$response = $client-&gt;patch(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PATCHapi-reservas--id--cancelar">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Reserva cancelada correctamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Ya cancelada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;La reserva ya est&aacute; cancelada&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Fecha pasada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No se puede cancelar una reserva del d&iacute;a actual o pasada&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-reservas--id--cancelar" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-reservas--id--cancelar"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-reservas--id--cancelar"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-reservas--id--cancelar" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-reservas--id--cancelar">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-reservas--id--cancelar" data-method="PATCH"
      data-path="api/reservas/{id}/cancelar"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-reservas--id--cancelar', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-reservas--id--cancelar"
                    onclick="tryItOut('PATCHapi-reservas--id--cancelar');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-reservas--id--cancelar"
                    onclick="cancelTryOut('PATCHapi-reservas--id--cancelar');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-reservas--id--cancelar"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/reservas/{id}/cancelar</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PATCHapi-reservas--id--cancelar"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-reservas--id--cancelar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-reservas--id--cancelar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PATCHapi-reservas--id--cancelar"
               value="1"
               data-component="url">
    <br>
<p>ID de la reserva. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="reservas-GETapi-reservas-hoy">Reservas de hoy (Operador)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna todas las reservas activas del día actual para validación en puerto.
Requiere rol operador o admin.</p>

<span id="example-requests-GETapi-reservas-hoy">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reservas-hoy?embarcacion_id=1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reservas-hoy"
);

const params = {
    "embarcacion_id": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/reservas-hoy';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'embarcacion_id' =&gt; '1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-reservas-hoy">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;fecha&quot;: &quot;2026-04-13&quot;,
            &quot;total_personas&quot;: 2,
            &quot;estado&quot;: &quot;confirmada&quot;,
            &quot;boleto&quot;: {
                &quot;codigo_qr&quot;: &quot;01KP2EZ650JKEFGPEZHGQWM5PD&quot;
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reservas-hoy" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reservas-hoy"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reservas-hoy"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reservas-hoy" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reservas-hoy">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reservas-hoy" data-method="GET"
      data-path="api/reservas-hoy"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reservas-hoy', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reservas-hoy"
                    onclick="tryItOut('GETapi-reservas-hoy');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reservas-hoy"
                    onclick="cancelTryOut('GETapi-reservas-hoy');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reservas-hoy"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reservas-hoy</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-reservas-hoy"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reservas-hoy"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reservas-hoy"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="embarcacion_id"                data-endpoint="GETapi-reservas-hoy"
               value="1"
               data-component="query">
    <br>
<p>Filtrar por embarcación específica. Example: <code>1</code></p>
            </div>
                </form>

                    <h2 id="reservas-GETapi-admin-reservas">Listar todas las reservas (Admin)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna todas las reservas del sistema con filtros opcionales. Paginado.
Requiere rol admin.</p>

<span id="example-requests-GETapi-admin-reservas">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/reservas?fecha=2026-05-10&amp;embarcacion_id=1&amp;estado=confirmada&amp;per_page=10" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/reservas"
);

const params = {
    "fecha": "2026-05-10",
    "embarcacion_id": "1",
    "estado": "confirmada",
    "per_page": "10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/reservas';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'fecha' =&gt; '2026-05-10',
            'embarcacion_id' =&gt; '1',
            'estado' =&gt; 'confirmada',
            'per_page' =&gt; '10',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-reservas">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [],
    &quot;current_page&quot;: 1,
    &quot;total&quot;: 0,
    &quot;per_page&quot;: 20
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-reservas" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-reservas"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-reservas"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-reservas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-reservas">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-reservas" data-method="GET"
      data-path="api/admin/reservas"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-reservas', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-reservas"
                    onclick="tryItOut('GETapi-admin-reservas');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-reservas"
                    onclick="cancelTryOut('GETapi-admin-reservas');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-reservas"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/reservas</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-reservas"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-admin-reservas"
               value="2026-05-10"
               data-component="query">
    <br>
<p>Filtrar por fecha (YYYY-MM-DD). Example: <code>2026-05-10</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="embarcacion_id"                data-endpoint="GETapi-admin-reservas"
               value="1"
               data-component="query">
    <br>
<p>Filtrar por embarcación. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>estado</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="estado"                data-endpoint="GETapi-admin-reservas"
               value="confirmada"
               data-component="query">
    <br>
<p>Filtrar por estado: confirmada, cancelada, completada. Example: <code>confirmada</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-admin-reservas"
               value="10"
               data-component="query">
    <br>
<p>Resultados por página (default: 20). Example: <code>10</code></p>
            </div>
                </form>

                    <h2 id="reservas-PATCHapi-admin-reservas--id--cancelar">Cancelar reserva</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Cancela una reserva del usuario autenticado. Solo se puede cancelar si la fecha es posterior a hoy.
Invalida automáticamente el boleto asociado.</p>

<span id="example-requests-PATCHapi-admin-reservas--id--cancelar">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://localhost:8000/api/admin/reservas/1/cancelar" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/reservas/1/cancelar"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PATCH",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/reservas/1/cancelar';
$response = $client-&gt;patch(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PATCHapi-admin-reservas--id--cancelar">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Reserva cancelada correctamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Ya cancelada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;La reserva ya est&aacute; cancelada&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Fecha pasada):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No se puede cancelar una reserva del d&iacute;a actual o pasada&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-admin-reservas--id--cancelar" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-admin-reservas--id--cancelar"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-admin-reservas--id--cancelar"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-admin-reservas--id--cancelar" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-admin-reservas--id--cancelar">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-admin-reservas--id--cancelar" data-method="PATCH"
      data-path="api/admin/reservas/{id}/cancelar"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-admin-reservas--id--cancelar', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-admin-reservas--id--cancelar"
                    onclick="tryItOut('PATCHapi-admin-reservas--id--cancelar');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-admin-reservas--id--cancelar"
                    onclick="cancelTryOut('PATCHapi-admin-reservas--id--cancelar');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-admin-reservas--id--cancelar"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/reservas/{id}/cancelar</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PATCHapi-admin-reservas--id--cancelar"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-admin-reservas--id--cancelar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-admin-reservas--id--cancelar"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PATCHapi-admin-reservas--id--cancelar"
               value="1"
               data-component="url">
    <br>
<p>ID de la reserva. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="reservas-DELETEapi-admin-reservas--id-">Eliminar reserva (Admin)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Elimina una reserva y su boleto asociado. Requiere rol admin.</p>

<span id="example-requests-DELETEapi-admin-reservas--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost:8000/api/admin/reservas/1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/reservas/1"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/reservas/1';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-reservas--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Reserva eliminada correctamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-admin-reservas--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-reservas--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-reservas--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-reservas--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-reservas--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-reservas--id-" data-method="DELETE"
      data-path="api/admin/reservas/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-reservas--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-reservas--id-"
                    onclick="tryItOut('DELETEapi-admin-reservas--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-reservas--id-"
                    onclick="cancelTryOut('DELETEapi-admin-reservas--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-reservas--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/reservas/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-admin-reservas--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-reservas--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-reservas--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-admin-reservas--id-"
               value="1"
               data-component="url">
    <br>
<p>ID de la reserva. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="reservas-PATCHapi-admin-reservas--id--restore">Restaurar reserva (Admin)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Restaura una reserva eliminada junto con su boleto. Requiere rol admin.</p>

<span id="example-requests-PATCHapi-admin-reservas--id--restore">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://localhost:8000/api/admin/reservas/1/restore" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/reservas/1/restore"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PATCH",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/reservas/1/restore';
$response = $client-&gt;patch(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PATCHapi-admin-reservas--id--restore">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Reserva restaurada correctamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-admin-reservas--id--restore" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-admin-reservas--id--restore"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-admin-reservas--id--restore"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-admin-reservas--id--restore" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-admin-reservas--id--restore">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-admin-reservas--id--restore" data-method="PATCH"
      data-path="api/admin/reservas/{id}/restore"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-admin-reservas--id--restore', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-admin-reservas--id--restore"
                    onclick="tryItOut('PATCHapi-admin-reservas--id--restore');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-admin-reservas--id--restore"
                    onclick="cancelTryOut('PATCHapi-admin-reservas--id--restore');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-admin-reservas--id--restore"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/reservas/{id}/restore</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PATCHapi-admin-reservas--id--restore"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-admin-reservas--id--restore"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-admin-reservas--id--restore"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PATCHapi-admin-reservas--id--restore"
               value="1"
               data-component="url">
    <br>
<p>ID de la reserva eliminada. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="boletos">Boletos</h1>

    <p>Endpoints para consulta, descarga y validación de boletos de embarque.</p>

                                <h2 id="boletos-POSTapi-validar-boleto--codigo-">Validar boleto QR</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Valida un boleto escaneando su código QR en el puerto de embarque.
Requiere rol operador o admin.
Verifica que el boleto sea válido, no haya sido usado y corresponda a la fecha de hoy.
Marca el boleto como usado al validarlo.</p>

<span id="example-requests-POSTapi-validar-boleto--codigo-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/validar-boleto/01KP2EZ650JKEFGPEZHGQWM5PD" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/validar-boleto/01KP2EZ650JKEFGPEZHGQWM5PD"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/validar-boleto/01KP2EZ650JKEFGPEZHGQWM5PD';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-validar-boleto--codigo-">
            <blockquote>
            <p>Example response (200, Válido):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;valido&quot;: true,
    &quot;message&quot;: &quot;Boleto validado correctamente&quot;,
    &quot;embarcacion&quot;: &quot;Lancha Uleam I&quot;,
    &quot;pasajeros&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;nombre&quot;: &quot;Juan P&eacute;rez&quot;,
            &quot;cedula&quot;: &quot;0950638675&quot;,
            &quot;tipo&quot;: &quot;estudiante&quot;
        }
    ],
    &quot;total&quot;: 2
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Ya usado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;valido&quot;: false,
    &quot;error&quot;: &quot;Boleto ya fue usado&quot;,
    &quot;usado_en&quot;: &quot;13/04/2026 21:55&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Cancelado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;valido&quot;: false,
    &quot;error&quot;: &quot;Boleto cancelado&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Fecha incorrecta):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;valido&quot;: false,
    &quot;error&quot;: &quot;El boleto no corresponde a la fecha de hoy&quot;,
    &quot;fecha_reserva&quot;: &quot;2026-05-10&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-validar-boleto--codigo-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-validar-boleto--codigo-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-validar-boleto--codigo-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-validar-boleto--codigo-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-validar-boleto--codigo-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-validar-boleto--codigo-" data-method="POST"
      data-path="api/validar-boleto/{codigo}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-validar-boleto--codigo-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-validar-boleto--codigo-"
                    onclick="tryItOut('POSTapi-validar-boleto--codigo-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-validar-boleto--codigo-"
                    onclick="cancelTryOut('POSTapi-validar-boleto--codigo-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-validar-boleto--codigo-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/validar-boleto/{codigo}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-validar-boleto--codigo-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-validar-boleto--codigo-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-validar-boleto--codigo-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>codigo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="codigo"                data-endpoint="POSTapi-validar-boleto--codigo-"
               value="01KP2EZ650JKEFGPEZHGQWM5PD"
               data-component="url">
    <br>
<p>Código QR del boleto. Example: <code>01KP2EZ650JKEFGPEZHGQWM5PD</code></p>
            </div>
                    </form>

                    <h2 id="boletos-GETapi-boletos--id-">Ver boleto</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna el detalle de un boleto. Solo el dueño de la reserva o un admin puede verlo.</p>

<span id="example-requests-GETapi-boletos--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/boletos/1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/boletos/1"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/boletos/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-boletos--id-">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;codigo_qr&quot;: &quot;01KP2EZ650JKEFGPEZHGQWM5PD&quot;,
        &quot;estado&quot;: &quot;valido&quot;,
        &quot;pdf_url&quot;: null,
        &quot;usado_en&quot;: null
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (403):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No autorizado&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-boletos--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-boletos--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-boletos--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-boletos--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-boletos--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-boletos--id-" data-method="GET"
      data-path="api/boletos/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-boletos--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-boletos--id-"
                    onclick="tryItOut('GETapi-boletos--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-boletos--id-"
                    onclick="cancelTryOut('GETapi-boletos--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-boletos--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/boletos/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-boletos--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-boletos--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-boletos--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-boletos--id-"
               value="1"
               data-component="url">
    <br>
<p>ID del boleto. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="boletos-GETapi-boletos--id--pdf">Descargar PDF del boleto</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Genera y retorna el PDF del boleto de embarque con código QR integrado.
Solo el dueño de la reserva o un admin puede descargarlo.
El PDF se guarda en storage para futuras descargas.</p>

<span id="example-requests-GETapi-boletos--id--pdf">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/boletos/1/pdf" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/boletos/1/pdf"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/boletos/1/pdf';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-boletos--id--pdf">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>
<code>Binary data -  El archivo PDF del boleto.</code>
 </pre>
            <blockquote>
            <p>Example response (403):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No autorizado&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-boletos--id--pdf" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-boletos--id--pdf"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-boletos--id--pdf"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-boletos--id--pdf" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-boletos--id--pdf">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-boletos--id--pdf" data-method="GET"
      data-path="api/boletos/{id}/pdf"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-boletos--id--pdf', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-boletos--id--pdf"
                    onclick="tryItOut('GETapi-boletos--id--pdf');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-boletos--id--pdf"
                    onclick="cancelTryOut('GETapi-boletos--id--pdf');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-boletos--id--pdf"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/boletos/{id}/pdf</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-boletos--id--pdf"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-boletos--id--pdf"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-boletos--id--pdf"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-boletos--id--pdf"
               value="1"
               data-component="url">
    <br>
<p>ID del boleto. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="actividad">Actividad</h1>

    <p>Endpoints para registro de actividad del usuario autenticado.
Flutter debe llamar a este endpoint para registrar acciones importantes
como login, creación de reservas, descarga de boletos, etc.</p>

                                <h2 id="actividad-POSTapi-actividad">Registrar actividad</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Registra una acción del usuario autenticado para auditoría institucional.
La IP se captura automáticamente del servidor.
Recomendado llamar desde Flutter en: login, reserva creada, boleto descargado, logout.</p>

<span id="example-requests-POSTapi-actividad">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/actividad" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"accion\": \"login\",
    \"descripcion\": \"Inicio de sesión exitoso desde app móvil\",
    \"dispositivo\": \"Samsung Galaxy S23 Android 14\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/actividad"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "accion": "login",
    "descripcion": "Inicio de sesión exitoso desde app móvil",
    "dispositivo": "Samsung Galaxy S23 Android 14"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/actividad';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'accion' =&gt; 'login',
            'descripcion' =&gt; 'Inicio de sesión exitoso desde app móvil',
            'dispositivo' =&gt; 'Samsung Galaxy S23 Android 14',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-actividad">
            <blockquote>
            <p>Example response (201, Registrado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Actividad registrada&quot;,
    &quot;actividad&quot;: {
        &quot;id&quot;: 1,
        &quot;accion&quot;: &quot;login&quot;,
        &quot;descripcion&quot;: &quot;Inicio de sesi&oacute;n exitoso desde app m&oacute;vil&quot;,
        &quot;ip&quot;: &quot;127.0.0.1&quot;,
        &quot;dispositivo&quot;: &quot;Samsung Galaxy S23 Android 14&quot;,
        &quot;fecha&quot;: &quot;13/04/2026 22:10&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;accion&quot;: [
            &quot;The accion field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-actividad" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-actividad"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-actividad"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-actividad" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-actividad">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-actividad" data-method="POST"
      data-path="api/actividad"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-actividad', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-actividad"
                    onclick="tryItOut('POSTapi-actividad');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-actividad"
                    onclick="cancelTryOut('POSTapi-actividad');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-actividad"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/actividad</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-actividad"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-actividad"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-actividad"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>accion</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="accion"                data-endpoint="POSTapi-actividad"
               value="login"
               data-component="body">
    <br>
<p>Nombre de la acción realizada (max 100 caracteres). Example: <code>login</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>descripcion</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="descripcion"                data-endpoint="POSTapi-actividad"
               value="Inicio de sesión exitoso desde app móvil"
               data-component="body">
    <br>
<p>Descripción detallada de la acción (max 500 caracteres). Example: <code>Inicio de sesión exitoso desde app móvil</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>dispositivo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="dispositivo"                data-endpoint="POSTapi-actividad"
               value="Samsung Galaxy S23 Android 14"
               data-component="body">
    <br>
<p>Nombre o descripción del dispositivo (max 255 caracteres). Example: <code>Samsung Galaxy S23 Android 14</code></p>
        </div>
        </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>ID de la actividad registrada.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>accion</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Acción registrada.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>descripcion</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Descripción de la acción.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ip</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>IP desde donde se realizó la acción.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>dispositivo</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Dispositivo desde donde se realizó la acción.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
<br>
<p>Fecha y hora formateada (d/m/Y H:i).</p>
        </div>
                    <h1 id="admin-dashboard">Admin — Dashboard</h1>

    <p>Endpoints para el panel de control administrativo.
Proveen estadísticas en tiempo real y reportes históricos del sistema.
Todos los endpoints requieren rol admin.</p>

                                <h2 id="admin-dashboard-GETapi-admin-dashboard">Resumen general</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna un resumen completo del estado actual del sistema:
totales generales, actividad del día, mes actual, próximas reservas y ocupación por embarcación.
La ocupación se calcula con una sola query optimizada para evitar N+1.</p>

<span id="example-requests-GETapi-admin-dashboard">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/dashboard" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/dashboard"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/dashboard';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-dashboard">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;totales&quot;: {
        &quot;embarcaciones&quot;: 4,
        &quot;disponibles&quot;: 3,
        &quot;mantenimiento&quot;: 1,
        &quot;usuarios&quot;: 5,
        &quot;reservas_total&quot;: 10,
        &quot;pasajeros_total&quot;: 25
    },
    &quot;hoy&quot;: {
        &quot;reservas&quot;: 2,
        &quot;pasajeros&quot;: 5,
        &quot;canceladas&quot;: 0
    },
    &quot;mes_actual&quot;: {
        &quot;reservas&quot;: 8,
        &quot;pasajeros&quot;: 20,
        &quot;canceladas&quot;: 1
    },
    &quot;proximas_reservas&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;fecha&quot;: &quot;2026-04-13&quot;,
            &quot;embarcacion&quot;: &quot;Lancha Uleam I&quot;,
            &quot;usuario&quot;: &quot;Estudiante Test&quot;,
            &quot;personas&quot;: 2,
            &quot;estado&quot;: &quot;confirmada&quot;
        }
    ],
    &quot;ocupacion_hoy&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;nombre&quot;: &quot;Lancha Uleam I&quot;,
            &quot;capacidad&quot;: 25,
            &quot;estado&quot;: &quot;disponible&quot;,
            &quot;reservados&quot;: 10,
            &quot;disponibles&quot;: 15,
            &quot;porcentaje&quot;: 40
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-dashboard" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-dashboard"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-dashboard"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-dashboard" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-dashboard">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-dashboard" data-method="GET"
      data-path="api/admin/dashboard"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-dashboard', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-dashboard"
                    onclick="tryItOut('GETapi-admin-dashboard');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-dashboard"
                    onclick="cancelTryOut('GETapi-admin-dashboard');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-dashboard"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/dashboard</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-dashboard"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-dashboard"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-dashboard"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="admin-dashboard-GETapi-admin-estadisticas">Estadísticas por rango de fechas</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna estadísticas detalladas filtradas por rango de fechas.
Por defecto retorna el mes actual.
Incluye: reservas por día, por embarcación, por tipo de pasajero, por estado y top usuarios.</p>

<span id="example-requests-GETapi-admin-estadisticas">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/estadisticas?desde=2026-04-01&amp;hasta=2026-04-30" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"desde\": \"2026-04-14T23:32:05\",
    \"hasta\": \"2107-05-14\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/estadisticas"
);

const params = {
    "desde": "2026-04-01",
    "hasta": "2026-04-30",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "desde": "2026-04-14T23:32:05",
    "hasta": "2107-05-14"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/estadisticas';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'desde' =&gt; '2026-04-01',
            'hasta' =&gt; '2026-04-30',
        ],
        'json' =&gt; [
            'desde' =&gt; '2026-04-14T23:32:05',
            'hasta' =&gt; '2107-05-14',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-estadisticas">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;rango&quot;: {
        &quot;desde&quot;: &quot;2026-04-01&quot;,
        &quot;hasta&quot;: &quot;2026-04-30&quot;
    },
    &quot;por_dia&quot;: [
        {
            &quot;fecha&quot;: &quot;2026-04-13&quot;,
            &quot;total_reservas&quot;: 2,
            &quot;total_pasajeros&quot;: 5
        }
    ],
    &quot;por_embarcacion&quot;: [
        {
            &quot;embarcacion&quot;: &quot;Lancha Uleam I&quot;,
            &quot;total_reservas&quot;: 5,
            &quot;total_pasajeros&quot;: 12
        }
    ],
    &quot;por_tipo&quot;: [
        {
            &quot;tipo&quot;: &quot;estudiante&quot;,
            &quot;total&quot;: 8
        },
        {
            &quot;tipo&quot;: &quot;externo&quot;,
            &quot;total&quot;: 3
        }
    ],
    &quot;por_estado&quot;: [
        {
            &quot;estado&quot;: &quot;confirmada&quot;,
            &quot;total&quot;: 8
        },
        {
            &quot;estado&quot;: &quot;cancelada&quot;,
            &quot;total&quot;: 1
        }
    ],
    &quot;top_usuarios&quot;: [
        {
            &quot;nombre&quot;: &quot;Estudiante Test&quot;,
            &quot;email&quot;: &quot;estudiante@uleam.edu.ec&quot;,
            &quot;total_reservas&quot;: 3
        }
    ]
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Fechas inválidas):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;hasta&quot;: [
            &quot;The hasta field must be a date after or equal to desde.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-estadisticas" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-estadisticas"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-estadisticas"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-estadisticas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-estadisticas">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-estadisticas" data-method="GET"
      data-path="api/admin/estadisticas"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-estadisticas', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-estadisticas"
                    onclick="tryItOut('GETapi-admin-estadisticas');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-estadisticas"
                    onclick="cancelTryOut('GETapi-admin-estadisticas');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-estadisticas"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/estadisticas</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-estadisticas"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-estadisticas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-estadisticas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>desde</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="desde"                data-endpoint="GETapi-admin-estadisticas"
               value="2026-04-01"
               data-component="query">
    <br>
<p>Fecha de inicio (YYYY-MM-DD). Default: primer día del mes actual. Example: <code>2026-04-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>hasta</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="hasta"                data-endpoint="GETapi-admin-estadisticas"
               value="2026-04-30"
               data-component="query">
    <br>
<p>Fecha de fin (YYYY-MM-DD). Default: hoy. Example: <code>2026-04-30</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>desde</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="desde"                data-endpoint="GETapi-admin-estadisticas"
               value="2026-04-14T23:32:05"
               data-component="body">
    <br>
<p>validation.date. Example: <code>2026-04-14T23:32:05</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>hasta</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="hasta"                data-endpoint="GETapi-admin-estadisticas"
               value="2107-05-14"
               data-component="body">
    <br>
<p>validation.date validation.after_or_equal. Example: <code>2107-05-14</code></p>
        </div>
        </form>

                <h1 id="admin-usuarios">Admin — Usuarios</h1>

    <p>Endpoints para gestión completa de usuarios del sistema.
Todos los endpoints requieren rol admin.</p>

                                <h2 id="admin-usuarios-GETapi-admin-usuarios">Listar usuarios</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna todos los usuarios del sistema con filtros opcionales. Paginado.</p>

<span id="example-requests-GETapi-admin-usuarios">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/usuarios?activo=1&amp;rol=usuario&amp;buscar=Juan&amp;per_page=10" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/usuarios"
);

const params = {
    "activo": "1",
    "rol": "usuario",
    "buscar": "Juan",
    "per_page": "10",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/usuarios';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'activo' =&gt; '1',
            'rol' =&gt; 'usuario',
            'buscar' =&gt; 'Juan',
            'per_page' =&gt; '10',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-usuarios">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 3,
            &quot;cedula&quot;: &quot;1300000003&quot;,
            &quot;nombre&quot;: &quot;Estudiante Test&quot;,
            &quot;email&quot;: &quot;estudiante@uleam.edu.ec&quot;,
            &quot;activo&quot;: true,
            &quot;rol&quot;: &quot;usuario&quot;,
            &quot;dias_para_vencer&quot;: 85,
            &quot;miembro_desde&quot;: &quot;09/04/2026&quot;
        }
    ],
    &quot;current_page&quot;: 1,
    &quot;total&quot;: 3,
    &quot;per_page&quot;: 20
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-usuarios" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-usuarios"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-usuarios"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-usuarios" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-usuarios">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-usuarios" data-method="GET"
      data-path="api/admin/usuarios"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-usuarios', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-usuarios"
                    onclick="tryItOut('GETapi-admin-usuarios');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-usuarios"
                    onclick="cancelTryOut('GETapi-admin-usuarios');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-usuarios"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/usuarios</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-usuarios"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-usuarios"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-usuarios"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>activo</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="GETapi-admin-usuarios" style="display: none">
            <input type="radio" name="activo"
                   value="1"
                   data-endpoint="GETapi-admin-usuarios"
                   data-component="query"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-admin-usuarios" style="display: none">
            <input type="radio" name="activo"
                   value="0"
                   data-endpoint="GETapi-admin-usuarios"
                   data-component="query"             >
            <code>false</code>
        </label>
    <br>
<p>Filtrar por estado: true=activos, false=inactivos. Example: <code>true</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>rol</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="rol"                data-endpoint="GETapi-admin-usuarios"
               value="usuario"
               data-component="query">
    <br>
<p>Filtrar por rol: admin, operador, usuario. Example: <code>usuario</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>buscar</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="buscar"                data-endpoint="GETapi-admin-usuarios"
               value="Juan"
               data-component="query">
    <br>
<p>Buscar por nombre, cédula o email. Example: <code>Juan</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-admin-usuarios"
               value="10"
               data-component="query">
    <br>
<p>Resultados por página (default: 20). Example: <code>10</code></p>
            </div>
                </form>

                    <h2 id="admin-usuarios-POSTapi-admin-usuarios">Crear usuario</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Crea un nuevo usuario desde el panel de administración.
Permite asignar cualquier rol directamente.
La cédula debe ser ecuatoriana válida.</p>

<span id="example-requests-POSTapi-admin-usuarios">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/admin/usuarios" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"cedula\": \"1300000099\",
    \"name\": \"María García\",
    \"email\": \"maria@uleam.edu.ec\",
    \"rol\": \"operador\",
    \"password\": \"Temporal123!\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/usuarios"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "cedula": "1300000099",
    "name": "María García",
    "email": "maria@uleam.edu.ec",
    "rol": "operador",
    "password": "Temporal123!"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/usuarios';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'cedula' =&gt; '1300000099',
            'name' =&gt; 'María García',
            'email' =&gt; 'maria@uleam.edu.ec',
            'rol' =&gt; 'operador',
            'password' =&gt; 'Temporal123!',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-usuarios">
            <blockquote>
            <p>Example response (201, Creado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Usuario creado correctamente&quot;,
    &quot;usuario&quot;: {
        &quot;id&quot;: 5,
        &quot;cedula&quot;: &quot;1300000099&quot;,
        &quot;nombre&quot;: &quot;Mar&iacute;a Garc&iacute;a&quot;,
        &quot;email&quot;: &quot;maria@uleam.edu.ec&quot;,
        &quot;activo&quot;: true,
        &quot;rol&quot;: &quot;operador&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Cédula inválida):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;cedula&quot;: [
            &quot;La c&eacute;dula ecuatoriana no es v&aacute;lida.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Email duplicado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-admin-usuarios" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-usuarios"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-usuarios"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-usuarios" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-usuarios">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-usuarios" data-method="POST"
      data-path="api/admin/usuarios"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-usuarios', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-usuarios"
                    onclick="tryItOut('POSTapi-admin-usuarios');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-usuarios"
                    onclick="cancelTryOut('POSTapi-admin-usuarios');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-usuarios"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/usuarios</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-admin-usuarios"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-usuarios"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-usuarios"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cedula</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cedula"                data-endpoint="POSTapi-admin-usuarios"
               value="1300000099"
               data-component="body">
    <br>
<p>Cédula ecuatoriana válida de 10 dígitos. Example: <code>1300000099</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-admin-usuarios"
               value="María García"
               data-component="body">
    <br>
<p>Nombre completo. Example: <code>María García</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-admin-usuarios"
               value="maria@uleam.edu.ec"
               data-component="body">
    <br>
<p>Correo electrónico único. Example: <code>maria@uleam.edu.ec</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rol</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="rol"                data-endpoint="POSTapi-admin-usuarios"
               value="operador"
               data-component="body">
    <br>
<p>Rol asignado: admin, operador, usuario. Example: <code>operador</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-admin-usuarios"
               value="Temporal123!"
               data-component="body">
    <br>
<p>Contraseña inicial mínimo 8 caracteres. Example: <code>Temporal123!</code></p>
        </div>
        </form>

                    <h2 id="admin-usuarios-GETapi-admin-usuarios--id-">Ver usuario</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna el detalle de un usuario con sus reservas asociadas.</p>

<span id="example-requests-GETapi-admin-usuarios--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/usuarios/3" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/usuarios/3"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/usuarios/3';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-usuarios--id-">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;usuario&quot;: {
        &quot;id&quot;: 3,
        &quot;cedula&quot;: &quot;1300000003&quot;,
        &quot;nombre&quot;: &quot;Estudiante Test&quot;,
        &quot;email&quot;: &quot;estudiante@uleam.edu.ec&quot;,
        &quot;activo&quot;: true,
        &quot;rol&quot;: &quot;usuario&quot;
    },
    &quot;total_reservas&quot;: 5
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-usuarios--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-usuarios--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-usuarios--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-usuarios--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-usuarios--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-usuarios--id-" data-method="GET"
      data-path="api/admin/usuarios/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-usuarios--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-usuarios--id-"
                    onclick="tryItOut('GETapi-admin-usuarios--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-usuarios--id-"
                    onclick="cancelTryOut('GETapi-admin-usuarios--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-usuarios--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/usuarios/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-usuarios--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-usuarios--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-usuarios--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-admin-usuarios--id-"
               value="3"
               data-component="url">
    <br>
<p>ID del usuario. Example: <code>3</code></p>
            </div>
                    </form>

                    <h2 id="admin-usuarios-PATCHapi-admin-usuarios--id--toggle">Activar / Desactivar usuario</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Alterna el estado activo/inactivo de un usuario.
Al desactivar, se cierran todas sus sesiones activas.
No se puede desactivar la propia cuenta.</p>

<span id="example-requests-PATCHapi-admin-usuarios--id--toggle">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://localhost:8000/api/admin/usuarios/3/toggle" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/usuarios/3/toggle"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PATCH",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/usuarios/3/toggle';
$response = $client-&gt;patch(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PATCHapi-admin-usuarios--id--toggle">
            <blockquote>
            <p>Example response (200, Desactivado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Usuario desactivado correctamente&quot;,
    &quot;activo&quot;: false
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Activado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Usuario activado correctamente&quot;,
    &quot;activo&quot;: true
}</code>
 </pre>
            <blockquote>
            <p>Example response (400):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No puedes desactivar tu propia cuenta&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-admin-usuarios--id--toggle" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-admin-usuarios--id--toggle"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-admin-usuarios--id--toggle"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-admin-usuarios--id--toggle" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-admin-usuarios--id--toggle">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-admin-usuarios--id--toggle" data-method="PATCH"
      data-path="api/admin/usuarios/{id}/toggle"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-admin-usuarios--id--toggle', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-admin-usuarios--id--toggle"
                    onclick="tryItOut('PATCHapi-admin-usuarios--id--toggle');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-admin-usuarios--id--toggle"
                    onclick="cancelTryOut('PATCHapi-admin-usuarios--id--toggle');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-admin-usuarios--id--toggle"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/usuarios/{id}/toggle</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PATCHapi-admin-usuarios--id--toggle"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-admin-usuarios--id--toggle"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-admin-usuarios--id--toggle"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PATCHapi-admin-usuarios--id--toggle"
               value="3"
               data-component="url">
    <br>
<p>ID del usuario. Example: <code>3</code></p>
            </div>
                    </form>

                    <h2 id="admin-usuarios-PATCHapi-admin-usuarios--id--rol">Cambiar rol</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Cambia el rol de un usuario del sistema.
No se puede cambiar el propio rol.</p>

<span id="example-requests-PATCHapi-admin-usuarios--id--rol">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://localhost:8000/api/admin/usuarios/3/rol" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"rol\": \"operador\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/usuarios/3/rol"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "rol": "operador"
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/usuarios/3/rol';
$response = $client-&gt;patch(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'rol' =&gt; 'operador',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PATCHapi-admin-usuarios--id--rol">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Rol actualizado correctamente&quot;,
    &quot;usuario&quot;: {
        &quot;id&quot;: 3,
        &quot;nombre&quot;: &quot;Estudiante Test&quot;,
        &quot;rol&quot;: &quot;operador&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (400):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No puedes cambiar tu propio rol&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Rol inválido):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;rol&quot;: [
            &quot;The selected rol is invalid.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-admin-usuarios--id--rol" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-admin-usuarios--id--rol"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-admin-usuarios--id--rol"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-admin-usuarios--id--rol" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-admin-usuarios--id--rol">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-admin-usuarios--id--rol" data-method="PATCH"
      data-path="api/admin/usuarios/{id}/rol"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-admin-usuarios--id--rol', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-admin-usuarios--id--rol"
                    onclick="tryItOut('PATCHapi-admin-usuarios--id--rol');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-admin-usuarios--id--rol"
                    onclick="cancelTryOut('PATCHapi-admin-usuarios--id--rol');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-admin-usuarios--id--rol"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/usuarios/{id}/rol</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PATCHapi-admin-usuarios--id--rol"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-admin-usuarios--id--rol"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-admin-usuarios--id--rol"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PATCHapi-admin-usuarios--id--rol"
               value="3"
               data-component="url">
    <br>
<p>ID del usuario. Example: <code>3</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rol</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="rol"                data-endpoint="PATCHapi-admin-usuarios--id--rol"
               value="operador"
               data-component="body">
    <br>
<p>Nuevo rol: admin, operador, usuario. Example: <code>operador</code></p>
        </div>
        </form>

                    <h2 id="admin-usuarios-DELETEapi-admin-usuarios--id-">Eliminar usuario</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Elimina un usuario del sistema.
No se puede eliminar la propia cuenta.
No se puede eliminar si tiene reservas futuras activas.
Cierra todas las sesiones del usuario antes de eliminarlo.</p>

<span id="example-requests-DELETEapi-admin-usuarios--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost:8000/api/admin/usuarios/3" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/usuarios/3"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/usuarios/3';
$response = $client-&gt;delete(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-usuarios--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Usuario eliminado correctamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Propia cuenta):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No puedes eliminar tu propia cuenta&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Reservas activas):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;El usuario tiene reservas futuras activas&quot;,
    &quot;reservas_futuras&quot;: 2
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Recurso no encontrado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-admin-usuarios--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-usuarios--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-usuarios--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-usuarios--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-usuarios--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-usuarios--id-" data-method="DELETE"
      data-path="api/admin/usuarios/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-usuarios--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-usuarios--id-"
                    onclick="tryItOut('DELETEapi-admin-usuarios--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-usuarios--id-"
                    onclick="cancelTryOut('DELETEapi-admin-usuarios--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-usuarios--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/usuarios/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-admin-usuarios--id-"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-usuarios--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-usuarios--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-admin-usuarios--id-"
               value="3"
               data-component="url">
    <br>
<p>ID del usuario. Example: <code>3</code></p>
            </div>
                    </form>

                <h1 id="admin-reportes">Admin — Reportes</h1>

    <p>Endpoints para generación y descarga de reportes en Excel y PDF.
Todos los endpoints requieren rol admin.</p>

                                <h2 id="admin-reportes-GETapi-admin-reportes-excel-pasajeros">Excel — pasajeros por fecha</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Genera y descarga un archivo Excel con el listado completo de pasajeros
de todas las reservas activas para una fecha específica.
Incluye: N° reserva, embarcación, titular, nombre pasajero, cédula, tipo, carrera, teléfono, email, estado.</p>

<span id="example-requests-GETapi-admin-reportes-excel-pasajeros">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/reportes/excel-pasajeros?fecha=2026-05-10&amp;embarcacion_id=1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"fecha\": \"2026-04-14T23:32:05\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/reportes/excel-pasajeros"
);

const params = {
    "fecha": "2026-05-10",
    "embarcacion_id": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "fecha": "2026-04-14T23:32:05"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/reportes/excel-pasajeros';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'fecha' =&gt; '2026-05-10',
            'embarcacion_id' =&gt; '1',
        ],
        'json' =&gt; [
            'fecha' =&gt; '2026-04-14T23:32:05',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-reportes-excel-pasajeros">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>
<code>Binary data -  Archivo Excel (.xlsx) con listado de pasajeros.</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;fecha&quot;: [
            &quot;The fecha field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-reportes-excel-pasajeros" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-reportes-excel-pasajeros"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-reportes-excel-pasajeros"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-reportes-excel-pasajeros" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-reportes-excel-pasajeros">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-reportes-excel-pasajeros" data-method="GET"
      data-path="api/admin/reportes/excel-pasajeros"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-reportes-excel-pasajeros', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-reportes-excel-pasajeros"
                    onclick="tryItOut('GETapi-admin-reportes-excel-pasajeros');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-reportes-excel-pasajeros"
                    onclick="cancelTryOut('GETapi-admin-reportes-excel-pasajeros');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-reportes-excel-pasajeros"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/reportes/excel-pasajeros</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-reportes-excel-pasajeros"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-reportes-excel-pasajeros"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-reportes-excel-pasajeros"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-admin-reportes-excel-pasajeros"
               value="2026-05-10"
               data-component="query">
    <br>
<p>Fecha de embarque (YYYY-MM-DD). Example: <code>2026-05-10</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="embarcacion_id"                data-endpoint="GETapi-admin-reportes-excel-pasajeros"
               value="1"
               data-component="query">
    <br>
<p>Filtrar por embarcación específica. Example: <code>1</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-admin-reportes-excel-pasajeros"
               value="2026-04-14T23:32:05"
               data-component="body">
    <br>
<p>validation.date. Example: <code>2026-04-14T23:32:05</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="embarcacion_id"                data-endpoint="GETapi-admin-reportes-excel-pasajeros"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the embarcaciones table.</p>
        </div>
        </form>

                    <h2 id="admin-reportes-GETapi-admin-reportes-excel-reservas">Excel — reservas por rango de fechas</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Genera y descarga un archivo Excel con el listado de reservas en un rango de fechas.
Incluye: N° reserva, fecha, embarcación, titular, cédula, total pasajeros, listado de pasajeros, estado.</p>

<span id="example-requests-GETapi-admin-reportes-excel-reservas">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/reportes/excel-reservas?desde=2026-05-01&amp;hasta=2026-05-31&amp;embarcacion_id=1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"desde\": \"2026-04-14T23:32:05\",
    \"hasta\": \"2107-05-14\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/reportes/excel-reservas"
);

const params = {
    "desde": "2026-05-01",
    "hasta": "2026-05-31",
    "embarcacion_id": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "desde": "2026-04-14T23:32:05",
    "hasta": "2107-05-14"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/reportes/excel-reservas';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'desde' =&gt; '2026-05-01',
            'hasta' =&gt; '2026-05-31',
            'embarcacion_id' =&gt; '1',
        ],
        'json' =&gt; [
            'desde' =&gt; '2026-04-14T23:32:05',
            'hasta' =&gt; '2107-05-14',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-reportes-excel-reservas">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>
<code>Binary data -  Archivo Excel (.xlsx) con listado de reservas.</code>
 </pre>
            <blockquote>
            <p>Example response (422, Fechas inválidas):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;hasta&quot;: [
            &quot;The hasta field must be a date after or equal to desde.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-reportes-excel-reservas" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-reportes-excel-reservas"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-reportes-excel-reservas"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-reportes-excel-reservas" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-reportes-excel-reservas">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-reportes-excel-reservas" data-method="GET"
      data-path="api/admin/reportes/excel-reservas"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-reportes-excel-reservas', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-reportes-excel-reservas"
                    onclick="tryItOut('GETapi-admin-reportes-excel-reservas');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-reportes-excel-reservas"
                    onclick="cancelTryOut('GETapi-admin-reportes-excel-reservas');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-reportes-excel-reservas"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/reportes/excel-reservas</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>desde</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="desde"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="2026-05-01"
               data-component="query">
    <br>
<p>Fecha de inicio del rango (YYYY-MM-DD). Example: <code>2026-05-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>hasta</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="hasta"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="2026-05-31"
               data-component="query">
    <br>
<p>Fecha de fin del rango (YYYY-MM-DD). Example: <code>2026-05-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="embarcacion_id"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="1"
               data-component="query">
    <br>
<p>Filtrar por embarcación específica. Example: <code>1</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>desde</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="desde"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="2026-04-14T23:32:05"
               data-component="body">
    <br>
<p>validation.date. Example: <code>2026-04-14T23:32:05</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>hasta</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="hasta"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value="2107-05-14"
               data-component="body">
    <br>
<p>validation.date validation.after_or_equal. Example: <code>2107-05-14</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="embarcacion_id"                data-endpoint="GETapi-admin-reportes-excel-reservas"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the embarcaciones table.</p>
        </div>
        </form>

                    <h2 id="admin-reportes-GETapi-admin-reportes-pdf-manifiesto">PDF — manifiesto de embarque</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Genera y retorna el manifiesto oficial de embarque en PDF para una fecha específica.
Incluye logo institucional, listado detallado de reservas con pasajeros, y totales.
Útil para el operador en el puerto como documento de control.</p>

<span id="example-requests-GETapi-admin-reportes-pdf-manifiesto">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/admin/reportes/pdf-manifiesto?fecha=2026-04-13&amp;embarcacion_id=1" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"fecha\": \"2026-04-14T23:32:05\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/admin/reportes/pdf-manifiesto"
);

const params = {
    "fecha": "2026-04-13",
    "embarcacion_id": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "fecha": "2026-04-14T23:32:05"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/admin/reportes/pdf-manifiesto';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
        'query' =&gt; [
            'fecha' =&gt; '2026-04-13',
            'embarcacion_id' =&gt; '1',
        ],
        'json' =&gt; [
            'fecha' =&gt; '2026-04-14T23:32:05',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-reportes-pdf-manifiesto">
            <blockquote>
            <p>Example response (200, Éxito):</p>
        </blockquote>
                <pre>
<code>Binary data -  Archivo PDF con el manifiesto de embarque.</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;fecha&quot;: [
            &quot;The fecha field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-reportes-pdf-manifiesto" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-reportes-pdf-manifiesto"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-reportes-pdf-manifiesto"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-reportes-pdf-manifiesto" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-reportes-pdf-manifiesto">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-reportes-pdf-manifiesto" data-method="GET"
      data-path="api/admin/reportes/pdf-manifiesto"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-reportes-pdf-manifiesto', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-reportes-pdf-manifiesto"
                    onclick="tryItOut('GETapi-admin-reportes-pdf-manifiesto');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-reportes-pdf-manifiesto"
                    onclick="cancelTryOut('GETapi-admin-reportes-pdf-manifiesto');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-reportes-pdf-manifiesto"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/reportes/pdf-manifiesto</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-admin-reportes-pdf-manifiesto"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-reportes-pdf-manifiesto"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-reportes-pdf-manifiesto"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-admin-reportes-pdf-manifiesto"
               value="2026-04-13"
               data-component="query">
    <br>
<p>Fecha de embarque (YYYY-MM-DD). Example: <code>2026-04-13</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="embarcacion_id"                data-endpoint="GETapi-admin-reportes-pdf-manifiesto"
               value="1"
               data-component="query">
    <br>
<p>Filtrar por embarcación específica. Si no se especifica, incluye todas. Example: <code>1</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fecha</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fecha"                data-endpoint="GETapi-admin-reportes-pdf-manifiesto"
               value="2026-04-14T23:32:05"
               data-component="body">
    <br>
<p>validation.date. Example: <code>2026-04-14T23:32:05</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>embarcacion_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="embarcacion_id"                data-endpoint="GETapi-admin-reportes-pdf-manifiesto"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the embarcaciones table.</p>
        </div>
        </form>

                <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-user">GET api/user</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-user">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/user" \
    --header "Authorization: Bearer {TU_TOKEN_AQUI}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/user"
);

const headers = {
    "Authorization": "Bearer {TU_TOKEN_AQUI}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost:8000/api/user';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer {TU_TOKEN_AQUI}',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETapi-user">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No autenticado&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user" data-method="GET"
      data-path="api/user"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user"
                    onclick="tryItOut('GETapi-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user"
                    onclick="cancelTryOut('GETapi-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-user"
               value="Bearer {TU_TOKEN_AQUI}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {TU_TOKEN_AQUI}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                            </div>
            </div>
</div>
</body>
</html>
