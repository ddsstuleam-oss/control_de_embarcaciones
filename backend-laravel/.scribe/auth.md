# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {TU_TOKEN_AQUI}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Obtén el token haciendo <b>POST /api/login</b> con tu cédula y contraseña. El token tiene una sola sesión activa a la vez.
