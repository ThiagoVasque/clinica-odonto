<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcedimentoResource\Pages;
use App\Models\Procedimento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProcedimentoResource extends Resource
{
    protected static ?string $model = Procedimento::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase'; // Ícone de maleta para serviços

    // Ajustes de Sidebar e Títulos
    protected static ?string $modelLabel = 'Procedimento';
    protected static ?string $pluralModelLabel = 'Procedimentos';
    protected static ?string $navigationGroup = 'Configurações';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalhes do Serviço')
                    ->description('Defina os valores padrão para os tratamentos da clínica')
                    ->schema([
                        Forms\Components\TextInput::make('descricao')
                            ->label('Descrição do Procedimento')
                            ->required()
                            ->placeholder('Ex: Limpeza, Canal, Extração...'),
                        Forms\Components\TextInput::make('valor_base')
                            ->label('Valor Sugerido')
                            ->numeric()
                            ->prefix('R$')
                            ->required()
                            ->placeholder('0,00'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descricao')
                    ->label('Procedimento')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valor_base')
                    ->label('Preço Base')
                    ->money('BRL')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),
            ])
            ->actions([
                // Agora padronizado com ActionGroup
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Alterar Preço'),
                    Tables\Actions\DeleteAction::make()
                        ->label('Remover Serviço'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->tooltip('Opções'),
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
            'index' => Pages\ListProcedimentos::route('/'),
            'create' => Pages\CreateProcedimento::route('/create'),
            'edit' => Pages\EditProcedimento::route('/{record}/edit'),
        ];
    }
}