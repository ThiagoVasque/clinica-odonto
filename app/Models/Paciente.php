<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $fillable = ['nome', 'cpf', 'data_nascimento', 'telefone', 'email', 'historico_medico'];

    public function orcamentos()
    {
        return $this->hasMany(Orcamento::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
