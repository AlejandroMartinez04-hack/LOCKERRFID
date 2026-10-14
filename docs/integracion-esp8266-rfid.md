# Integración ESP8266, RC522 y API RFID

## Endpoint

`POST /api/rfid/validar`

El endpoint usa el token del dispositivo, no un token Sanctum. Las rutas administrativas existentes continúan protegidas por Sanctum y el middleware de roles.

### Cabeceras

```http
Content-Type: application/json
Accept: application/json
X-Device-Token: <token-del-dispositivo-registrado>
```

### Cuerpo

```json
{
  "dispositivo_id": 1,
  "locker_id": 4,
  "uid": "04A1B2C3"
}
```

`dispositivo_id` identifica el registro cuyo token se valida; `locker_id` selecciona el locker que se intenta abrir. El esquema actual no relaciona un dispositivo con un locker, por lo que el firmware debe enviar ambos IDs. `uid` debe coincidir con el UID almacenado en `tarjetas_rfid`.

Registra el dispositivo desde el panel administrativo y configura en el firmware su ID y el token generado para ese dispositivo. No incluyas el token en el código fuente público. Para instalaciones fuera de una red local de confianza, usa HTTPS.

## Respuestas

### Acceso autorizado — HTTP 200

```json
{
  "autorizado": true,
  "mensaje": "Acceso autorizado.",
  "data": {
    "lectura_id": 25,
    "locker_id": 4,
    "locker_numero": "RFID-101"
  }
}
```

El firmware solo debe considerar la apertura si recibe HTTP 200 y `autorizado: true`.

### Acceso denegado — HTTP 403

```json
{
  "autorizado": false,
  "mensaje": "Sin asignación activa para este locker.",
  "data": {
    "lectura_id": 26,
    "locker_id": 4,
    "locker_numero": "RFID-101"
  }
}
```

También se deniega una tarjeta inactiva, un usuario inactivo o un dispositivo inactivo. Una tarjeta no registrada produce HTTP 403 con el motivo correspondiente.

### Token inválido — HTTP 401

```json
{
  "autorizado": false,
  "mensaje": "El token del dispositivo no es válido.",
  "data": {
    "lectura_id": 27,
    "locker_id": null,
    "locker_numero": null
  }
}
```

Los intentos de un dispositivo conocido con token inválido quedan registrados con el identificador del dispositivo y sin atribuirles tarjeta ni locker. Un `dispositivo_id` inexistente o datos de entrada incompletos producen HTTP 422; los intentos incompletos de un dispositivo autenticado también quedan auditados.

Las lecturas se guardan en `lecturas_rfid` con `tipo_acceso=apertura`, `resultado=permitido|denegado` y el motivo. La autorización consulta la política existente: un usuario solo puede acceder a su locker con asignación activa; un administrador activo conserva el acceso global establecido por esa política. Leer una tarjeta no modifica el locker ni la asignación.
