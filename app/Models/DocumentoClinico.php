<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoClinico extends Model
{
    // Como a tabela tem um nome personalizado, avisamos o Model
    protected $table = 'documentos_clinicos';

    protected $fillable = [
        'paciente_id', 
        'tipo', 
        'conteudo'
    ];

    // Relacionamento: O documento pertence a um paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}