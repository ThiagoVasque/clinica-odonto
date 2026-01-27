<?php

namespace App\Filament\Resources\AgendamentoResource\Pages;

use App\Filament\Resources\AgendamentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAgendamentos extends ListRecords
{
    protected static string $resource = AgendamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Agendamento'),
        ];
    }

    /**
     * Cria as abas superiores para filtrar a agenda rapidamente
     */
    public function getTabs(): array
    {
        return [
            'hoje' => Tab::make('Hoje')
                ->icon('heroicon-m-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('data_hora', now())),
                
            'amanha' => Tab::make('Amanhã')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('data_hora', now()->addDay())),
                
            'todos' => Tab::make('Todos os Lançamentos')
                ->icon('heroicon-m-list-bullet'),
        ];
    }

    /**
     * Define qual aba vem aberta por padrão
     */
    public function getDefaultActiveTab(): string | int | null
    {
        return 'hoje';
    }
}