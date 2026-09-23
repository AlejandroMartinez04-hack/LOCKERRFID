<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lecturas_rfid', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarjeta_rfid_id')->nullable()->constrained('tarjetas_rfid')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('locker_id')->nullable()->constrained('lockers')->nullOnDelete();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->cascadeOnDelete();
            $table->string('tipo_acceso');
            $table->string('resultado');
            $table->string('motivo');
            $table->dateTime('fecha_hora');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecturas_rfid');
    }
};
