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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orcamento_id')->constrained('orcamentos');
            $table->string('pagbank_id')->nullable();
            $table->string('metodo_pagamento'); // pix, boleto, cartao
            $table->string('status_pagamento'); // aguardando, pago, recusado
            $table->string('url_boleto')->nullable();
            $table->decimal('valor_pago', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
