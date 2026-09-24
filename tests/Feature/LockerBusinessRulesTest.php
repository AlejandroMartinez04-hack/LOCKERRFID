<?php

use App\Models\AsignacionLocker;
use App\Models\Locker;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('creates an active assignment and occupies the locker', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $locker = Locker::create([
        'numero' => 'TEST-101',
        'ubicacion' => 'Pruebas',
        'estado' => 'disponible',
    ]);

    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/asignaciones-lockers', [
        'locker_id' => $locker->id,
        'usuario_id' => $user->id,
        'fecha_inicio' => now()->toDateTimeString(),
    ]);

    $response->assertCreated();
    expect($locker->refresh()->estado)->toBe('ocupado')
        ->and(AsignacionLocker::where('locker_id', $locker->id)->value('estado'))->toBe('activa');
});

test('rejects assignments for unavailable lockers', function (string $estado) {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $locker = Locker::create([
        'numero' => 'TEST-'.strtoupper($estado),
        'ubicacion' => 'Pruebas',
        'estado' => $estado,
    ]);

    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/asignaciones-lockers', [
        'locker_id' => $locker->id,
        'usuario_id' => $user->id,
        'fecha_inicio' => now()->toDateTimeString(),
    ]);

    $response->assertStatus(409);
})->with(['ocupado', 'mantenimiento', 'bloqueado']);

test('rejects a second active assignment for the same locker', function () {
    $admin = User::factory()->admin()->create();
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();
    $locker = Locker::create([
        'numero' => 'TEST-102',
        'ubicacion' => 'Pruebas',
        'estado' => 'disponible',
    ]);

    AsignacionLocker::create([
        'locker_id' => $locker->id,
        'usuario_id' => $firstUser->id,
        'fecha_inicio' => now(),
        'estado' => 'activa',
    ]);
    $locker->update(['estado' => 'ocupado']);

    $response = $this->actingAs($admin, 'sanctum')->postJson('/api/asignaciones-lockers', [
        'locker_id' => $locker->id,
        'usuario_id' => $secondUser->id,
        'fecha_inicio' => now()->toDateTimeString(),
    ]);

    $response->assertStatus(409);
});

test('keeps locker state consistent with active assignments', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $availableLocker = Locker::create([
        'numero' => 'TEST-STATE-1',
        'ubicacion' => 'Pruebas',
        'estado' => 'disponible',
    ]);
    $occupiedLocker = Locker::create([
        'numero' => 'TEST-STATE-2',
        'ubicacion' => 'Pruebas',
        'estado' => 'ocupado',
    ]);
    AsignacionLocker::create([
        'locker_id' => $occupiedLocker->id,
        'usuario_id' => $user->id,
        'fecha_inicio' => now(),
        'estado' => 'activa',
    ]);

    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/lockers/{$availableLocker->id}", ['estado' => 'ocupado'])
        ->assertStatus(409);
    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/lockers/{$occupiedLocker->id}", ['estado' => 'disponible'])
        ->assertStatus(409);
});

test('finalizing and cancelling assignments release their lockers', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $finalizedLocker = Locker::create([
        'numero' => 'TEST-103',
        'ubicacion' => 'Pruebas',
        'estado' => 'ocupado',
    ]);
    $cancelledLocker = Locker::create([
        'numero' => 'TEST-104',
        'ubicacion' => 'Pruebas',
        'estado' => 'ocupado',
    ]);
    $finalized = AsignacionLocker::create([
        'locker_id' => $finalizedLocker->id,
        'usuario_id' => $user->id,
        'fecha_inicio' => now()->subDay(),
        'estado' => 'activa',
    ]);
    $cancelled = AsignacionLocker::create([
        'locker_id' => $cancelledLocker->id,
        'usuario_id' => $user->id,
        'fecha_inicio' => now()->subDay(),
        'estado' => 'activa',
    ]);

    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/asignaciones-lockers/{$finalized->id}", ['estado' => 'finalizada'])
        ->assertOk();
    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/asignaciones-lockers/{$cancelled->id}", ['estado' => 'cancelada'])
        ->assertOk();

    expect($finalized->refresh()->fecha_fin)->not->toBeNull()
        ->and($cancelled->refresh()->fecha_fin)->not->toBeNull()
        ->and($finalizedLocker->refresh()->estado)->toBe('disponible')
        ->and($cancelledLocker->refresh()->estado)->toBe('disponible');
});

test('a user can only view their active locker through mi-locker', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownLocker = Locker::create([
        'numero' => 'TEST-105',
        'ubicacion' => 'Pruebas',
        'estado' => 'ocupado',
    ]);
    $otherLocker = Locker::create([
        'numero' => 'TEST-106',
        'ubicacion' => 'Pruebas',
        'estado' => 'ocupado',
    ]);
    AsignacionLocker::create([
        'locker_id' => $ownLocker->id,
        'usuario_id' => $user->id,
        'fecha_inicio' => now(),
        'estado' => 'activa',
    ]);
    AsignacionLocker::create([
        'locker_id' => $otherLocker->id,
        'usuario_id' => $otherUser->id,
        'fecha_inicio' => now(),
        'estado' => 'activa',
    ]);

    $response = $this->actingAs($user, 'sanctum')->getJson('/api/mi-locker');

    $response->assertOk()->assertJsonPath('data.id', $ownLocker->id);
    expect(Gate::forUser($user)->allows('view', $ownLocker))->toBeTrue()
        ->and(Gate::forUser($user)->allows('view', $otherLocker))->toBeFalse();
});

test('a user without an active assignment receives not found from mi-locker', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/mi-locker')
        ->assertNotFound();
});
