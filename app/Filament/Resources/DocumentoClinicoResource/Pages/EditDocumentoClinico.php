<?php

namespace App\Filament\Resources\DocumentoClinicoResource\Pages;

use App\Filament\Resources\DocumentoClinicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentoClinico extends EditRecord
{
    protected static string $resource = DocumentoClinicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
