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
        Schema::create('prontuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->integer('numero_dente'); // Ex: 11, 12, 21...
            $table->string('face_dente')->nullable(); // Mesial, Distal, Oclusal, etc.
            $table->foreignId('procedimento_id')->constrained();
            $table->text('observacoes')->nullable();
            $table->enum('status', ['planejado', 'executado'])->default('planejado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prontuarios'); // Remova o acento aqui
    }
};
