<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendamentoResource\Pages;
use App\Models\Agendamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    
    // Traduções do Menu e Cabeçalhos
    protected static ?string $modelLabel = 'Agendamento';
    protected static ?string $pluralModelLabel = 'Agendamentos';
    protected static ?string $navigationGroup = 'Atendimento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Marcar Consulta')
                    ->description('Informe os dados para o novo agendamento')
                    ->schema([
                        Forms\Components\Select::make('paciente_id')
                            ->label('Paciente') // Traduzido
                            ->relationship('paciente', 'nome')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DateTimePicker::make('data_hora')
                            ->label('Data e Hora da Consulta')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->minutesStep(15)
                            ->seconds(false)
                            ->required()
                            ->locale('pt_BR'),
                        Forms\Components\Select::make('status')
                            ->label('Situação') // Traduzido
                            ->options([
                                'agendado' => 'Agendado',
                                'confirmado' => 'Confirmado',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                            ])->default('agendado'),
                        Forms\Components\Textarea::make('observacoes')
                            ->label('Observações')
                            ->placeholder('Ex: Paciente relatou dor no siso...')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paciente.nome')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data_hora')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'agendado' => 'info',
                        'confirmado' => 'success',
                        'concluido' => 'primary',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Status')
                    ->options([
                        'agendado' => 'Agendado',
                        'confirmado' => 'Confirmado',
                        'concluido' => 'Concluído',
                        'cancelado' => 'Cancelado',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Editar'), // Botão traduzido
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Excluir Selecionados'),
                ])->label('Ações em Massa'),
            ]);
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