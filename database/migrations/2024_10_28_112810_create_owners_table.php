<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id(); // ID del propietario
            $table->string('name'); // Nombre del propietario
            $table->string('email')->unique(); // Correo electrónico único
            $table->string('phone')->nullable(); // Número de teléfono opcional
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owners');
    }
};
