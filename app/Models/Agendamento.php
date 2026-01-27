<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = [
        'paciente_id',
        'titulo',
        'data_hora',
        'status',
        'observacoes',
        'cor',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}
