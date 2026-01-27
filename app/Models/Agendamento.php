<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = ['paciente_id', 'data_hora', 'status', 'observacoes'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}