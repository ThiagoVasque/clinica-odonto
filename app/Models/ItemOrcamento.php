<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrcamento extends Model
{
    protected $table = 'itens_orcamento'; // Define o nome correto da tabela
    protected $fillable = ['orcamento_id', 'procedimento_id', 'quantidade', 'valor_unitario'];

    public function orcamento()
    {
        return $this->belongsTo(Orcamento::class);
    }

    public function procedimento()
    {
        return $this->belongsTo(Procedimento::class);
    }
}