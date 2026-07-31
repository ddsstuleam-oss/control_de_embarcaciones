# Introduction

Documentación oficial de la API REST del sistema institucional de control y reservas de embarcaciones de la Facultad de Ciencias de la Vida y Tecnologías — Universidad Laica Eloy Alfaro de Manabí.

<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>

Esta documentación describe todos los endpoints disponibles en el sistema de control de embarcaciones FCVT-ULEAM.

## Autenticación
El sistema usa **Laravel Sanctum** con tokens Bearer. Para autenticarte:
1. Haz `POST /api/login` con tu cédula y contraseña
2. Copia el `token` de la respuesta
3. Úsalo en el header: `Authorization: Bearer {token}`

## Roles disponibles
- **admin** — acceso total al sistema
- **operador** — validación de boletos en puerto
- **usuario** — reservas y boletos propios

<aside>Los ejemplos de código están disponibles en bash, javascript y php en el panel derecho.</aside>

