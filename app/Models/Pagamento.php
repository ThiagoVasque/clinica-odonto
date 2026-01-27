<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    // Definimos os campos que podem ser preenchidos
    protected $fillable = [
        'orcamento_id', 
        'pagbank_id', 
        'metodo_pagamento', 
        'status_pagamento', 
        'url_boleto', 
        'valor_pago'
    ];

    // Relacionamento: Todo pagamento pertence a um orçamento
    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }
}