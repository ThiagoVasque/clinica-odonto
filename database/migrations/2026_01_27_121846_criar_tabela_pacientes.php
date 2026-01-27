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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->date('data_nascimento');
            $table->string('telefone');
            $table->string('email')->nullable();
            $table->text('historico_medico')->nullable(); // Anamnese/Alergias
            $table->timestamps();
        });
    }

    public function down(): void
    {
        //
    }
};
