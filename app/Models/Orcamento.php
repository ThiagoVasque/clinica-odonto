<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    protected $fillable = ['paciente_id', 'valor_total', 'status'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function itens()
    {
        return $this->hasMany(ItemOrcamento::class, 'orcamento_id');
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'orcamento_id');
    }
}