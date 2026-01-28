<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoClinico extends Model
{
    protected $table = 'documentos_clinicos';

    protected $fillable = [
        'paciente_id', 
        'tipo', 
        'conteudo'
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
}