<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LockerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $lockers = [
            [
                'id' => 1,
                'numero' => 'L-101',
                'ubicacion' => 'Edificio Central - Modulo A',
                'estado' => 'disponible',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'numero' => 'L-102',
                'ubicacion' => 'Edificio Central - Modulo A',
                'estado' => 'disponible',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'numero' => 'L-103',
                'ubicacion' => 'Edificio Central - Modulo A',
                'estado' => 'ocupado',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'numero' => 'L-104',
                'ubicacion' => 'Edificio Central - Modulo A',
                'estado' => 'ocupado',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'numero' => 'L-105',
                'ubicacion' => 'Edificio Central - Modulo A',
                'estado' => 'mantenimiento',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'numero' => 'L-201',
                'ubicacion' => 'Edificio Ingenieria - Modulo B',
                'estado' => 'disponible',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'numero' => 'L-202',
                'ubicacion' => 'Edificio Ingenieria - Modulo B',
                'estado' => 'bloqueado',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('lockers')->insert($lockers);
    }
}
