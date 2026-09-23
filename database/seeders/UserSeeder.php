<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('password123');

        $users = [
            [
                'id' => 1,
                'name' => 'Administrador Sistema',
                'email' => 'admin@silocker.local',
                'email_verified_at' => $now,
                'password' => $password,
                'rol' => 'admin',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Carlos Mendoza',
                'email' => 'carlos.mendoza@example.com',
                'email_verified_at' => $now,
                'password' => $password,
                'rol' => 'usuario',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'María Fernández',
                'email' => 'maria.fernandez@example.com',
                'email_verified_at' => $now,
                'password' => $password,
                'rol' => 'usuario',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Juan Morales',
                'email' => 'juan.morales@example.com',
                'email_verified_at' => $now,
                'password' => $password,
                'rol' => 'usuario',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Ana Gómez',
                'email' => 'ana.gomez@example.com',
                'email_verified_at' => $now,
                'password' => $password,
                'rol' => 'usuario',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Pedro Rivas',
                'email' => 'pedro.rivas@example.com',
                'email_verified_at' => $now,
                'password' => $password,
                'rol' => 'usuario',
                'estado' => 'inactivo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('users')->insert($users);
    }
}
