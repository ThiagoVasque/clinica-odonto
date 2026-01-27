<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentoClinicoResource\Pages;
use App\Filament\Resources\DocumentoClinicoResource\RelationManagers;
use App\Models\DocumentoClinico;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentoClinicoResource extends Resource
{
    protected static ?string $modelLabel = 'Documento Clínico';
    protected static ?string $pluralModelLabel = 'Receitas e Atestados';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Emissão de Documento')
                    ->description('Gere receitas e atestados rapidamente')
                    ->schema([
                        Forms\Components\Select::make('paciente_id')
                            ->relationship('paciente', 'nome')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Paciente'),

                        Forms\Components\Select::make('tipo')
                            ->options([
                                'receita' => 'Receita Médica',
                                'atestado' => 'Atestado Odontológico',
                                'declaracao' => 'Declaração de Comparecimento',
                            ])
                            ->required()
                            ->label('Tipo de Documento')
                            ->live() // Faz o formulário reagir à mudança
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $pacienteNome = \App\Models\Paciente::find($get('paciente_id'))?->nome ?? '...';

                                // Templates Automáticos
                                if ($state === 'atestado') {
                                    $set('conteudo', "Atesto para os devidos fins que o(a) Sr(a) **$pacienteNome** esteve em tratamento odontológico nesta data, necessitando de _____ dias de repouso.");
                                } elseif ($state === 'receita') {
                                    $set('conteudo', "Uso contínuo:\n1. ___________ - Tomar 01 comprimido a cada ___ horas.");
                                }
                            }),

                        Forms\Components\RichEditor::make('conteudo')
                            ->label('Conteúdo do Documento')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'orderedList',
                                'redo',
                                'undo',
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paciente.nome')
                    ->label('Paciente')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'receita' => 'info',
                        'atestado' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Emitido em')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('pdf')
                    ->label('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('documento.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDocumentoClinicos::route('/'),
            'create' => Pages\CreateDocumentoClinico::route('/create'),
            'edit' => Pages\EditDocumentoClinico::route('/{record}/edit'),
        ];
    }
}
