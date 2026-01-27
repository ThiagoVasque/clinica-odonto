<?php

namespace App\Filament\Resources\ProcedimentoResource\Pages;

use App\Filament\Resources\ProcedimentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcedimento extends EditRecord
{
    protected static string $resource = ProcedimentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
