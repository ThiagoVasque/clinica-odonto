<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcedimentoResource\Pages;
use App\Filament\Resources\ProcedimentoResource\RelationManagers;
use App\Models\Procedimento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProcedimentoResource extends Resource
{
    protected static ?string $model = Procedimento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Detalhes do Serviço')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('descricao')
                            ->label('Descrição do Procedimento')
                            ->required()
                            ->placeholder('Ex: Limpeza, Canal, Extração...'),
                        \Filament\Forms\Components\TextInput::make('valor_base')
                            ->label('Valor sugerido (R$)')
                            ->numeric()
                            ->prefix('R$')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('descricao')
                    ->label('Procedimento')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('valor_base')
                    ->label('Preço Base')
                    ->money('BRL') // Já coloca o R$ e as vírgulas automaticamente
                    ->sortable(),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProcedimentos::route('/'),
            'create' => Pages\CreateProcedimento::route('/create'),
            'edit' => Pages\EditProcedimento::route('/{record}/edit'),
        ];
    }
}
