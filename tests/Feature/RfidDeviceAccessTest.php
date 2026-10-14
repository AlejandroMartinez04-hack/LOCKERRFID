<?php

use App\Models\AsignacionLocker;
use App\Models\Dispositivo;
use App\Models\LecturaRfid;
use App\Models\Locker;
use App\Models\TarjetaRfid;
use App\Models\User;

beforeEach(function () {
    $this->device = Dispositivo::create([
        'nombre' => 'ESP8266 de pruebas',
        'ubicacion' => 'Laboratorio',
        'token' => 'test-device-token',
        'estado' => 'activo',
    ]);
    $this->locker = Locker::create([
        'numero' => 'RFID-101',
        'ubicacion' => 'Laboratorio',
        'estado' => 'ocupado',
    ]);
    $this->user = User::factory()->create();
    $this->headers = ['X-Device-Token' => 'test-device-token'];
});

function rfidAccessPayload(Dispositivo $device, Locker $locker, string $uid = '04A1B2C3'): array
{
    return [
        'dispositivo_id' => $device->id,
        'locker_id' => $locker->id,
        'uid' => $uid,
    ];
}

function createRfidCard(User $user, string $uid = '04A1B2C3', string $state = 'activa'): TarjetaRfid
{
    return TarjetaRfid::create([
        'usuario_id' => $user->id,
        'uid' => $uid,
        'estado' => $state,
    ]);
}

function assignRfidLocker(User $user, Locker $locker, array $attributes = []): AsignacionLocker
{
    return AsignacionLocker::create(array_merge([
        'usuario_id' => $user->id,
        'locker_id' => $locker->id,
        'fecha_inicio' => now()->subMinute(),
        'estado' => 'activa',
    ], $attributes));
}

test('an active registered device can authorize its assigned user', function () {
    $card = createRfidCard($this->user);
    assignRfidLocker($this->user, $this->locker);

    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertOk()
        ->assertJsonPath('autorizado', true)
        ->assertJsonPath('mensaje', 'Acceso autorizado.')
        ->assertJsonPath('data.locker_id', $this->locker->id);

    $reading = LecturaRfid::sole();
    expect($reading->tarjeta_rfid_id)->toBe($card->id)
        ->and($reading->usuario_id)->toBe($this->user->id)
        ->and($reading->resultado)->toBe('permitido')
        ->and($reading->tipo_acceso)->toBe('apertura')
        ->and($this->locker->refresh()->estado)->toBe('ocupado');
});

test('a token mismatch is rejected and recorded without trusting card or locker data', function () {
    $this->withHeaders(['X-Device-Token' => 'wrong-token'])
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertUnauthorized()
        ->assertJsonPath('autorizado', false)
        ->assertJsonPath('data.locker_id', null);

    $reading = LecturaRfid::sole();
    expect($reading->dispositivo_id)->toBe($this->device->id)
        ->and($reading->tarjeta_rfid_id)->toBeNull()
        ->and($reading->resultado)->toBe('denegado')
        ->and($reading->motivo)->toBe('Token de dispositivo inválido.');
});

test('an unknown card is denied and recorded', function () {
    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker, 'UNKNOWN'))
        ->assertForbidden()
        ->assertJsonPath('autorizado', false);

    expect(LecturaRfid::sole()->motivo)->toBe('Tarjeta RFID no registrada.');
});

test('an inactive card is denied and recorded', function () {
    $card = createRfidCard($this->user, state: 'inactiva');
    assignRfidLocker($this->user, $this->locker);

    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertForbidden()
        ->assertJsonPath('autorizado', false);

    expect(LecturaRfid::sole()->tarjeta_rfid_id)->toBe($card->id)
        ->and(LecturaRfid::sole()->motivo)->toBe('Tarjeta RFID inactiva.');
});

test('an inactive card owner is denied and recorded', function () {
    $inactiveUser = User::factory()->inactivo()->create();
    $card = createRfidCard($inactiveUser);
    assignRfidLocker($inactiveUser, $this->locker);

    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertForbidden()
        ->assertJsonPath('autorizado', false);

    expect(LecturaRfid::sole()->tarjeta_rfid_id)->toBe($card->id)
        ->and(LecturaRfid::sole()->usuario_id)->toBe($inactiveUser->id)
        ->and(LecturaRfid::sole()->motivo)->toBe('Usuario inactivo.');
});

test('a user cannot open another users locker', function () {
    createRfidCard($this->user);
    assignRfidLocker(User::factory()->create(), $this->locker);

    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertForbidden()
        ->assertJsonPath('autorizado', false)
        ->assertJsonPath('mensaje', 'Sin asignación activa para este locker.');

    expect(LecturaRfid::sole()->resultado)->toBe('denegado');
});

test('a user without a valid active assignment is denied and recorded', function () {
    createRfidCard($this->user);

    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertForbidden()
        ->assertJsonPath('autorizado', false);

    expect(LecturaRfid::sole()->motivo)->toBe('Sin asignación activa para este locker.');
});

test('an active administrator can access a locker without an assignment', function () {
    $admin = User::factory()->admin()->create();
    createRfidCard($admin);

    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertOk()
        ->assertJsonPath('autorizado', true);

    expect(LecturaRfid::sole()->resultado)->toBe('permitido');
});

test('an inactive device is denied and recorded', function () {
    $this->device->update(['estado' => 'inactivo']);

    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', rfidAccessPayload($this->device, $this->locker))
        ->assertForbidden()
        ->assertJsonPath('autorizado', false);

    expect(LecturaRfid::sole()->motivo)->toBe('Dispositivo inactivo.');
});

test('an authenticated device with incomplete access data is denied and audited', function () {
    $this->withHeaders($this->headers)
        ->postJson('/api/rfid/validar', ['dispositivo_id' => $this->device->id])
        ->assertUnprocessable()
        ->assertJsonPath('autorizado', false)
        ->assertJsonPath('data.lectura_id', LecturaRfid::sole()->id);

    expect(LecturaRfid::sole()->resultado)->toBe('denegado')
        ->and(LecturaRfid::sole()->locker_id)->toBeNull();
});
