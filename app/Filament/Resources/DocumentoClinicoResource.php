<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentoClinicoResource\Pages;
use App\Models\DocumentoClinico;
use App\Models\Paciente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentoClinicoResource extends Resource
{
    protected static ?string $model = DocumentoClinico::class;
    
    protected static ?string $modelLabel = 'Documento Clínico';
    protected static ?string $pluralModelLabel = 'Receitas e Atestados';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    // Organiza na sidebar junto com outros documentos
    protected static ?string $navigationGroup = 'Atendimento';

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
                            ->label('Tipo de Documento')
                            ->options([
                                'receita' => 'Receita Médica',
                                'atestado' => 'Atestado Odontológico',
                                'declaracao' => 'Declaração de Comparecimento',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $paciente = Paciente::find($get('paciente_id'));
                                $pacienteNome = $paciente?->nome ?? '...';

                                // Templates Automáticos melhorados
                                if ($state === 'atestado') {
                                    $set('conteudo', "Atesto para os devidos fins que o(a) Sr(a) <strong>$pacienteNome</strong> esteve em tratamento odontológico nesta data, necessitando de _____ dias de repouso.");
                                } elseif ($state === 'receita') {
                                    $set('conteudo', "<strong>Uso contínuo:</strong><br>1. ___________ - Tomar 01 comprimido a cada ___ horas.");
                                } elseif ($state === 'declaracao') {
                                    $set('conteudo', "Declaro para os devidos fins que o(a) Sr(a) <strong>$pacienteNome</strong> compareceu a esta clínica odontológica no dia de hoje, no período das ____:____ às ____:____.");
                                }
                            }),

                        Forms\Components\RichEditor::make('conteudo')
                            ->label('Conteúdo do Documento')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'blockquote', 'bold', 'bulletList', 'h2', 'h3', 
                                'italic', 'orderedList', 'redo', 'undo',
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'receita' => 'Receita',
                        'atestado' => 'Atestado',
                        'declaracao' => 'Declaração',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'receita' => 'info',
                        'atestado' => 'danger',
                        'declaracao' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Emitido em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc') // Mais recentes primeiro
            ->actions([
                // PADRONIZADO: ActionGroup para as ações de documento
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('pdf')
                        ->label('Imprimir PDF')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->url(fn($record) => route('documento.pdf', $record))
                        ->openUrlInNewTab(),
                        
                    Tables\Actions\EditAction::make()
                        ->label('Editar Conteúdo'),
                        
                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir Registro'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
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
            'index' => Pages\ListDocumentoClinicos::route('/'),
            'create' => Pages\CreateDocumentoClinico::route('/create'),
            'edit' => Pages\EditDocumentoClinico::route('/{record}/edit'),
        ];
    }
}