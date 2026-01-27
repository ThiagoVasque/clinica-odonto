<?php

namespace App\Filament\Widgets;

use App\Models\Agendamento; // IMPORTANTE: Não esqueça essa linha
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class ProximosAgendamentos extends BaseWidget
{
    protected static ?string $heading = 'Agenda do Dia';
    
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2; // Fica abaixo dos cartões de estatísticas

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Busca agendamentos de hoje
                Agendamento::query()
                    ->whereDate('data_hora', Carbon::today())
                    ->orderBy('data_hora', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('data_hora')
                    ->label('Horário')
                    ->dateTime('H:i')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('paciente.nome')
                    ->label('Paciente'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'agendado' => 'info',
                        'confirmado' => 'success',
                        'cancelado' => 'danger',
                        'concluido' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('Avisar')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn(Agendamento $record): string => "https://wa.me/55" . preg_replace('/[^0-9]/', '', $record->paciente->telefone) . "?text=" . urlencode("Olá " . $record->paciente->nome . ", confirmamos sua consulta hoje às " . Carbon::parse($record->data_hora)->format('H:i') . "?"))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('Ver')
                    ->label('Ver')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn(Agendamento $record): string => "/admin/agendamentos/{$record->id}/edit"),
            ]);
    }
}