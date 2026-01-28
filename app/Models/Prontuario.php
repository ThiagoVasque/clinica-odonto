<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prontuario extends Model
{
    // Forçamos o nome da tabela caso o Laravel tente procurar "prontuários" com acento
    protected $table = 'prontuarios';

    protected $fillable = [
        'paciente_id',
        'numero_dente',
        'face_dente',
        'procedimento_id',
        'observacoes',
        'status'
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function procedimento(): BelongsTo
    {
        return $this->belongsTo(Procedimento::class);
    }
}