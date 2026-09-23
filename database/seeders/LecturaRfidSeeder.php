<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LecturaRfidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $lecturas = [
            [
                'id' => 1,
                'tarjeta_rfid_id' => 2, // Tarjeta Carlos
                'usuario_id' => 2,      // Carlos Mendoza
                'locker_id' => 3,       // L-103 (su locker asignado)
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'usuario',
                'resultado' => 'permitido',
                'motivo' => 'Locker asignado al usuario',
                'fecha_hora' => $now->copy()->subDays(3)->setHour(8)->setMinute(15),
                'created_at' => $now->copy()->subDays(3)->setHour(8)->setMinute(15),
                'updated_at' => $now->copy()->subDays(3)->setHour(8)->setMinute(15),
            ],
            [
                'id' => 2,
                'tarjeta_rfid_id' => 3, // Tarjeta María
                'usuario_id' => 3,      // María Fernández
                'locker_id' => 4,       // L-104 (su locker asignado)
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'usuario',
                'resultado' => 'permitido',
                'motivo' => 'Locker asignado al usuario',
                'fecha_hora' => $now->copy()->subDays(1)->setHour(9)->setMinute(30),
                'created_at' => $now->copy()->subDays(1)->setHour(9)->setMinute(30),
                'updated_at' => $now->copy()->subDays(1)->setHour(9)->setMinute(30),
            ],
            [
                'id' => 3,
                'tarjeta_rfid_id' => 2, // Tarjeta Carlos
                'usuario_id' => 2,      // Carlos Mendoza
                'locker_id' => 4,       // L-104 (pertenece a María)
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'usuario',
                'resultado' => 'denegado',
                'motivo' => 'Locker pertenece a otro usuario',
                'fecha_hora' => $now->copy()->subHours(6),
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(6),
            ],
            [
                'id' => 4,
                'tarjeta_rfid_id' => null, // Tarjeta no autorizada / no registrada
                'usuario_id' => null,
                'locker_id' => 1,       // L-101
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'usuario',
                'resultado' => 'denegado',
                'motivo' => 'Tarjeta no autorizada',
                'fecha_hora' => $now->copy()->subHours(4),
                'created_at' => $now->copy()->subHours(4),
                'updated_at' => $now->copy()->subHours(4),
            ],
            [
                'id' => 5,
                'tarjeta_rfid_id' => 6, // Tarjeta Pedro
                'usuario_id' => 6,      // Pedro Rivas (estado: inactivo)
                'locker_id' => 1,       // L-101
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'usuario',
                'resultado' => 'denegado',
                'motivo' => 'Usuario inactivo',
                'fecha_hora' => $now->copy()->subHours(2),
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'id' => 6,
                'tarjeta_rfid_id' => 7, // Tarjeta inactiva de Carlos
                'usuario_id' => 2,      // Carlos Mendoza
                'locker_id' => 3,       // L-103
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'usuario',
                'resultado' => 'denegado',
                'motivo' => 'Tarjeta inactiva',
                'fecha_hora' => $now->copy()->subHours(1),
                'created_at' => $now->copy()->subHours(1),
                'updated_at' => $now->copy()->subHours(1),
            ],
            [
                'id' => 7,
                'tarjeta_rfid_id' => 1, // Tarjeta Admin
                'usuario_id' => 1,      // Admin
                'locker_id' => 3,       // L-103 (locker ocupado)
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'admin',
                'resultado' => 'permitido',
                'motivo' => 'Apertura administrativa',
                'fecha_hora' => $now->copy()->subMinutes(30),
                'created_at' => $now->copy()->subMinutes(30),
                'updated_at' => $now->copy()->subMinutes(30),
            ],
            [
                'id' => 8,
                'tarjeta_rfid_id' => 1, // Tarjeta Admin
                'usuario_id' => 1,      // Admin
                'locker_id' => 5,       // L-105 (locker en mantenimiento)
                'dispositivo_id' => 1,  // ESP32 Modulo A
                'tipo_acceso' => 'admin',
                'resultado' => 'permitido',
                'motivo' => 'Mantenimiento',
                'fecha_hora' => $now->copy()->subDays(2),
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
        ];

        DB::table('lecturas_rfid')->insert($lecturas);
    }
}
