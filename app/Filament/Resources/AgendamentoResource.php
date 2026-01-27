<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendamentoResource\Pages;
use App\Filament\Resources\AgendamentoResource\RelationManagers;
use App\Models\Agendamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Marcar Consulta')
                    ->schema([
                        \Filament\Forms\Components\Select::make('paciente_id')
                            ->relationship('paciente', 'nome') // Puxa os nomes dos pacientes cadastrados
                            ->searchable()
                            ->preload()
                            ->required(),
                        \Filament\Forms\Components\DateTimePicker::make('data_hora')
                            ->label('Data e Hora da Consulta')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->required(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'agendado' => 'Agendado',
                                'confirmado' => 'Confirmado',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                            ])->default('agendado'),
                        \Filament\Forms\Components\Textarea::make('observacoes')
                            ->label('Observações')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('paciente.nome')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('data_hora')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                \Filament\Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'agendado',
                        'success' => 'confirmado',
                        'primary' => 'concluido',
                        'danger' => 'cancelado',
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendamentos::route('/'),
            'create' => Pages\CreateAgendamento::route('/create'),
            'edit' => Pages\EditAgendamento::route('/{record}/edit'),
        ];
    }
}
