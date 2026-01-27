<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrcamentoResource\Pages;
use App\Models\Orcamento;
use App\Models\Procedimento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Filament\Forms\Get;

class OrcamentoResource extends Resource
{
    protected static ?string $model = Orcamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    // CORREÇÃO: Define o nome correto com acentuação na Sidebar
    protected static ?string $modelLabel = 'Orçamento';
    protected static ?string $pluralModelLabel = 'Orçamentos';
    protected static ?string $navigationGroup = 'Financeiro';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados do Orçamento')
                    ->schema([
                        Forms\Components\Select::make('paciente_id')
                            ->label('Paciente')
                            ->relationship('paciente', 'nome')
                            ->searchable()
                            ->preload()
                            ->required(),
                            
                        Forms\Components\Select::make('status')
                            ->label('Situação')
                            ->options([
                                'pendente' => 'Pendente',
                                'aprovado' => 'Aprovado',
                                'pago' => 'Pago',
                            ])
                            ->default('pendente')
                            ->required(),

                        Forms\Components\Repeater::make('itens')
                            ->label('Procedimentos do Orçamento')
                            ->relationship('itens')
                            ->schema([
                                Forms\Components\Select::make('procedimento_id')
                                    ->label('Procedimento')
                                    ->relationship('procedimento', 'descricao')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        // Busca o valor base do procedimento automaticamente
                                        $procedimento = Procedimento::find($state);
                                        $set('valor_unitario', $procedimento?->valor_base ?? 0);
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantidade')
                                    ->label('Qtd')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(),

                                Forms\Components\TextInput::make('valor_unitario')
                                    ->label('Preço Unitário')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required()
                                    ->live(),

                                // Campo informativo de Subtotal por item
                                Forms\Components\Placeholder::make('subtotal')
                                    ->label('Subtotal')
                                    ->content(function (Get $get) {
                                        $qtd = (float) ($get('quantidade') ?? 0);
                                        $valor = (float) ($get('valor_unitario') ?? 0);
                                        return 'R$ ' . number_format($qtd * $valor, 2, ',', '.');
                                    }),
                            ])
                            ->columns(5)
                            ->columnSpanFull()
                            ->grid(1) // Organiza melhor visualmente
                            ->addActionLabel('Adicionar Procedimento'),
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

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Situação')
                    ->color(fn(string $state): string => match ($state) {
                        'pendente' => 'danger',
                        'aprovado' => 'warning',
                        'pago' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                // Exibe o Valor Total direto na tabela
                Tables\Columns\TextColumn::make('total')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->state(function (Orcamento $record) {
                        return $record->itens->sum(fn($item) => $item->quantidade * $item->valor_unitario);
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Situação')
                    ->options([
                        'pendente' => 'Pendente',
                        'aprovado' => 'Aprovado',
                        'pago' => 'Pago',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('pdf')
                        ->label('Gerar PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->url(fn(Orcamento $record): string => route('orcamento.pdf', $record))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('whatsapp')
                        ->label('Enviar WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->url(function (Orcamento $record) {
                            $total = number_format($record->itens->sum(fn($i) => $i->quantidade * $i->valor_unitario), 2, ',', '.');
                            $texto = urlencode("Olá " . $record->paciente->nome . "! Segue o orçamento da JR Odontologia no valor total de R$ " . $total . ". Podemos agendar?");
                            $telefone = preg_replace('/\D/', '', $record->paciente->telefone);
                            return "https://wa.me/55{$telefone}?text={$texto}";
                        })
                        ->openUrlInNewTab(),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListOrcamentos::route('/'),
            'create' => Pages\CreateOrcamento::route('/create'),
            'edit' => Pages\EditOrcamento::route('/{record}/edit'),
        ];
    }
}