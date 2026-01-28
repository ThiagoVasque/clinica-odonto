<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    protected $fillable = ['nome', 'cpf', 'data_nascimento', 'telefone', 'email', 'historico_medico'];

    protected static function booted()
    {
        static::deleting(function ($paciente) {
            // Isso resolve o erro "Cannot delete or update a parent row"
            $paciente->documentosClinicos()->delete(); 
            $paciente->orcamentos()->delete();
            $paciente->agendamentos()->delete();
        });
    }

    // Adicione este relacionamento que estava faltando
    public function documentosClinicos(): HasMany
    {
        return $this->hasMany(DocumentoClinico::class, 'paciente_id');
    }

    public function orcamentos(): HasMany
    {
        return $this->hasMany(Orcamento::class);
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }
}