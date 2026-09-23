<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TarjetaRfidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $tarjetas = [
            [
                'id' => 1,
                'usuario_id' => 1, // Admin
                'uid' => 'A0B1C2D3',
                'estado' => 'activa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'usuario_id' => 2, // Carlos Mendoza
                'uid' => 'E4F5A6B7',
                'estado' => 'activa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'usuario_id' => 3, // María Fernández
                'uid' => '89C4D2E1',
                'estado' => 'activa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'usuario_id' => 4, // Juan Morales
                'uid' => '1F2E3D4C',
                'estado' => 'activa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'usuario_id' => 5, // Ana Gómez
                'uid' => '5A6B7C8D',
                'estado' => 'activa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'usuario_id' => 6, // Pedro Rivas (usuario inactivo)
                'uid' => '9E8D7C6B',
                'estado' => 'activa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'usuario_id' => 2, // Carlos Mendoza (tarjeta reportada/inactiva)
                'uid' => 'FA010203',
                'estado' => 'inactiva',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('tarjetas_rfid')->insert($tarjetas);
    }
}
