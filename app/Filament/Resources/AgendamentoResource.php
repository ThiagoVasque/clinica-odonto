<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendamentoResource\Pages;
use App\Models\Agendamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AgendamentoResource extends Resource
{
    protected static ?string $model = Agendamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $modelLabel = 'Agendamento';
    protected static ?string $pluralModelLabel = 'Agendamentos';
    protected static ?string $navigationGroup = 'Atendimento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Marcar Consulta')
                    ->schema([
                        Forms\Components\Select::make('paciente_id')
                            ->relationship('paciente', 'nome')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\DatePicker::make('data_agendamento') // Nome diferente do banco
                            ->label('Data')
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->live()
                            // Carrega a data do banco ao editar
                            ->formatStateUsing(fn($record) => $record?->data_hora?->format('Y-m-d'))
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('hora_agendamento') // Nome diferente do banco
                            ->label('Horário')
                            ->mask('99:99')
                            ->required()
                            ->live()
                            // Carrega a hora do banco ao editar
                            ->formatStateUsing(fn($record) => $record?->data_hora?->format('H:i'))
                            ->dehydrated(false),

                        // O campo REAL do banco fica escondido e processa os dados só no envio (dehydrate)
                        Forms\Components\Hidden::make('data_hora')
                            ->dehydrated(true)
                            ->dehydrateStateUsing(function ($get) {
                                $data = $get('data_agendamento');
                                $hora = $get('hora_agendamento');

                                if (!$data || !$hora) return null;
                                return "{$data} {$hora}";
                            }),

                        Forms\Components\Select::make('status')
                            ->options([
                                'agendado' => 'Agendado',
                                'confirmado' => 'Confirmado',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                            ])
                            ->default('agendado')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('data_hora', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('data_hora')
                    ->label('Horário')
                    ->dateTime('H:i')
                    ->description(fn(Agendamento $record): string => $record->data_hora->format('d/m/Y'))
                    ->color('primary')
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paciente.nome')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'agendado' => 'info',
                        'confirmado' => 'success',
                        'concluido' => 'gray',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('paciente.telefone')
                    ->label('Lembrete')
                    ->formatStateUsing(fn() => 'WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (Agendamento $record) {
                        if (!$record->paciente?->telefone) return null;
                        $texto = urlencode("Olá {$record->paciente->nome}, confirmamos sua consulta na JR Odontologia em {$record->data_hora->format('d/m')} às {$record->data_hora->format('H:i')}?");
                        return "https://wa.me/55" . preg_replace('/\D/', '', $record->paciente->telefone) . "?text={$texto}";
                    })
                    ->openUrlInNewTab(),
            ])
            ->filters([
                // Filtros de período para substituir o calendário
                Tables\Filters\Filter::make('data_hora')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('ate')->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['desde'], fn($query, $date) => $query->whereDate('data_hora', '>=', $date))
                            ->when($data['ate'], fn($query, $date) => $query->whereDate('data_hora', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Situação')
                    ->options([
                        'agendado' => 'Agendado',
                        'confirmado' => 'Confirmado',
                        'concluido' => 'Concluído',
                        'cancelado' => 'Cancelado',
                    ]),
            ])
            ->actions([
                // Ações rápidas para a Dra. não precisar entrar no "Editar"
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('confirmar')
                        ->label('Confirmar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(Agendamento $record) => $record->update(['status' => 'confirmado']))
                        ->requiresConfirmation(),
                    Tables\Actions\Action::make('cancelar')
                        ->label('Cancelar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn(Agendamento $record) => $record->update(['status' => 'cancelado']))
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
