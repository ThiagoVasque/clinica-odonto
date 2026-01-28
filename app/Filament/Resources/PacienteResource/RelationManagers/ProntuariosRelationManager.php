<?php

namespace App\Filament\Resources\PacienteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProntuariosRelationManager extends RelationManager
{
    protected static string $relationship = 'prontuarios';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('numero_dente')
                    ->label('Dente')
                    ->options([
                        'Superiores Direitos' => [18 => 18, 17 => 17, 16 => 16, 15 => 15, 14 => 14, 13 => 13, 12 => 12, 11 => 11],
                        'Superiores Esquerdos' => [21 => 21, 22 => 22, 23 => 23, 24 => 24, 25 => 25, 26 => 26, 27 => 27, 28 => 28],
                        'Inferiores Esquerdos' => [38 => 38, 37 => 37, 36 => 36, 35 => 35, 34 => 34, 33 => 33, 32 => 32, 31 => 31],
                        'Inferiores Direitos' => [41 => 41, 42 => 42, 43 => 43, 44 => 44, 45 => 45, 46 => 46, 47 => 47, 48 => 48],
                    ])
                    ->required(),

                Forms\Components\Select::make('procedimento_id')
                    ->label('Procedimento')
                    // Mude de 'nome' para 'descricao' aqui:
                    ->relationship('procedimento', 'descricao')
                    ->required()
                    ->preload()
                    ->searchable(),

                Forms\Components\Select::make('status')
                    ->options([
                        'planejado' => 'Planejado',
                        'executado' => 'Executado',
                    ])
                    ->default('planejado')
                    ->required(),

                Forms\Components\Textarea::make('observacoes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_dente')
            ->columns([
                // Mostra o número do dente (Ex: 11, 22)
                Tables\Columns\TextColumn::make('numero_dente')
                    ->label('Dente')
                    ->sortable(),

                // Puxa a descrição do procedimento do outro Model
                Tables\Columns\TextColumn::make('procedimento.descricao')
                    ->label('Procedimento')
                    ->searchable(),

                // Status colorido (Verde para executado, Amarelo para planejado)
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'planejado' => 'warning',
                        'executado' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->label('Status'),

                // Data formatada para o padrão brasileiro
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                // Filtro rápido para ver apenas o que está "Planejado"
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planejado' => 'Planejado',
                        'executado' => 'Executado',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Novo Registro'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
