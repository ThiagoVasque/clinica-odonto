<?php

use Illuminate\Support\Facades\Route;
use App\Models\DocumentoClinico;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Orcamento;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/documentos/{documento}/pdf', function (DocumentoClinico $documento) {
    // Tenta carregar a view. Se der erro de 'View not found', 
    // verifique se o arquivo está em resources/views/pdf/documento.blade.php
    $pdf = Pdf::loadView('pdf.documento', compact('documento'));
    
    return $pdf->stream("documento_{$documento->id}.pdf");
})->name('documento.pdf')->middleware('auth');

Route::get('/admin/orcamentos/{orcamento}/pdf', function (Orcamento $orcamento) {
    // Importante usar 'loadView' e passar o orcamento com os itens e procedimentos carregados
    $orcamento->load(['paciente', 'itens.procedimento']);
    $pdf = Pdf::loadView('pdf.orcamento', compact('orcamento'));
    return $pdf->stream("orcamento_{$orcamento->id}.pdf");
})->name('orcamento.pdf')->middleware('auth');