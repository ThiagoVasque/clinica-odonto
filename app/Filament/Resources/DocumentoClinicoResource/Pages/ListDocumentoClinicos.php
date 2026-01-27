<?php

namespace App\Filament\Resources\DocumentoClinicoResource\Pages;

use App\Filament\Resources\DocumentoClinicoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentoClinicos extends ListRecords
{
    protected static string $resource = DocumentoClinicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
