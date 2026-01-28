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
                            ->required()
                            ->live(),

                        Forms\Components\Repeater::make('itens')
                            ->relationship('itens')
                            ->schema([
                                Forms\Components\Select::make('procedimento_id')
                                    ->label('Procedimento')
                                    ->relationship('procedimento', 'descricao')
                                    ->required()
                                    ->live()
                                    ->distinct() // Evita selecionar o mesmo procedimento duas vezes
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems() // Estético: remove da lista o que já foi usado
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $procedimento = \App\Models\Procedimento::find($state);
                                        $set('valor_unitario', $procedimento?->valor_base ?? 0);
                                        self::atualizarTotal($get, $set);
                                    })
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('quantidade')
                                    ->label('Qtd')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Get $get, Set $set) => self::atualizarTotal($get, $set)),

                                Forms\Components\TextInput::make('valor_unitario')
                                    ->label('Preço Unitário')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->readOnly()
                                    ->extraInputAttributes(['class' => 'bg-gray-100']),
                            ])
                            ->live() // ESSENCIAL: Faz o repeater avisar o formulário sobre mudanças
                            ->afterStateUpdated(fn(Get $get, Set $set) => self::atualizarTotal($get, $set))
                            ->minItems(1)
                            ->deletable(fn(Get $get) => count($get('itens') ?? []) > 1)
                            ->columns(4)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('valor_total')
                            ->label('Valor Total do Orçamento')
                            ->numeric()
                            ->prefix('R$')
                            ->readOnly()
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    // Função centralizada para cálculo
    public static function atualizarTotal(Get $get, Set $set): void
    {
        // Buscamos os itens subindo dois níveis para garantir que pegamos o array completo do formulário
        $itens = $get('../../itens') ?? $get('itens') ?? [];
        $total = 0;

        foreach ($itens as $item) {
            $qtd = (float) ($item['quantidade'] ?? 0);
            $vlr = (float) ($item['valor_unitario'] ?? 0);
            $total += ($qtd * $vlr);
        }

        // Atualizamos o campo valor_total no nível da Section
        $set('../../valor_total', $total);

        // Fallback caso a função seja chamada fora do contexto do repeater
        $set('valor_total', $total);
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

                Tables\Columns\TextColumn::make('total')
                    ->label('Valor Total')
                    ->money('BRL')
                    ->state(fn(Orcamento $record) => $record->itens->sum(fn($item) => $item->quantidade * $item->valor_unitario)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
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
                            $texto = urlencode("Olá " . $record->paciente->nome . "! Segue o orçamento no valor de R$ " . $total);
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
