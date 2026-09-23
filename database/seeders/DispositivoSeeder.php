<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DispositivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $dispositivos = [
            [
                'id' => 1,
                'nombre' => 'ESP32 - Modulo Lockers A',
                'ubicacion' => 'Edificio Central - Pasillo 1',
                'token' => 'esp32_token_mod_a_8f3e2b1c4d',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nombre' => 'ESP32 - Modulo Lockers B',
                'ubicacion' => 'Edificio Ingenieria - Planta Baja',
                'token' => 'esp32_token_mod_b_9a1d5e7c3b',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('dispositivos')->insert($dispositivos);
    }
}
