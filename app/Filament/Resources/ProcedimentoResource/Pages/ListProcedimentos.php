<?php

namespace App\Filament\Resources\ProcedimentoResource\Pages;

use App\Filament\Resources\ProcedimentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcedimentos extends ListRecords
{
    protected static string $resource = ProcedimentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
