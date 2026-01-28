<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Prontuario;

class Paciente extends Model
{
    protected $fillable = ['nome', 'cpf', 'data_nascimento', 'telefone', 'email', 'historico_medico'];
    
    protected static function booted()
    {
        static::deleting(function ($paciente) {
            // Adicionei a limpeza dos prontuários aqui também!
            $paciente->prontuarios()->delete(); 
            $paciente->documentosClinicos()->delete(); 
            $paciente->orcamentos()->delete();
            $paciente->agendamentos()->delete();
        });
    }

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

    public function prontuarios(): HasMany
    {
        return $this->hasMany(Prontuario::class);
    }
}