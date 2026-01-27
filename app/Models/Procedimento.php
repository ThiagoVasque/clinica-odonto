<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedimento extends Model
{
    protected $fillable = ['descricao', 'valor_base'];

    public function itensOrcamento()
    {
        return $this->hasMany(ItemOrcamento::class);
    }
}