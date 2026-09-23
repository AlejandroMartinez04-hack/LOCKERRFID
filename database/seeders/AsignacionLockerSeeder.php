<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AsignacionLockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $asignaciones = [
            [
                'id' => 1,
                'locker_id' => 3, // L-103
                'usuario_id' => 2, // Carlos Mendoza
                'fecha_inicio' => $now->copy()->subDays(10),
                'fecha_fin' => null,
                'estado' => 'activa',
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],
            [
                'id' => 2,
                'locker_id' => 4, // L-104
                'usuario_id' => 3, // María Fernández
                'fecha_inicio' => $now->copy()->subDays(5),
                'fecha_fin' => null,
                'estado' => 'activa',
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id' => 3,
                'locker_id' => 1, // L-101 (hoy disponible)
                'usuario_id' => 4, // Juan Morales
                'fecha_inicio' => $now->copy()->subDays(30),
                'fecha_fin' => $now->copy()->subDays(15),
                'estado' => 'finalizada',
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(15),
            ],
            [
                'id' => 4,
                'locker_id' => 2, // L-102 (hoy disponible)
                'usuario_id' => 5, // Ana Gómez
                'fecha_inicio' => $now->copy()->subDays(20),
                'fecha_fin' => $now->copy()->subDays(2),
                'estado' => 'cancelada',
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(2),
            ],
        ];

        DB::table('asignaciones_lockers')->insert($asignaciones);
    }
}
