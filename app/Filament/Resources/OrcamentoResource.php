<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrcamentoResource\Pages;
use App\Models\Orcamento;
use App\Models\Procedimento; // Importante para o find($state)
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set; // Necessário para o $set funcionar
use Filament\Forms\Get;

class OrcamentoResource extends Resource
{
    protected static ?string $model = Orcamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator'; // Ícone mais apropriado

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Dados do Orçamento')
                    ->schema([
                        \Filament\Forms\Components\Select::make('paciente_id')
                            ->relationship('paciente', 'nome')
                            ->searchable()
                            ->preload()
                            ->required(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'pendente' => 'Pendente',
                                'aprovado' => 'Aprovado',
                                'pago' => 'Pago',
                            ])->default('pendente'),

                        \Filament\Forms\Components\Repeater::make('itens')
                            ->relationship('itens')
                            ->schema([
                                \Filament\Forms\Components\Select::make('procedimento_id')
                                    ->relationship('procedimento', 'descricao')
                                    ->required()
                                    ->live() // No Filament 3, usamos live() em vez de reactive()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        $procedimento = Procedimento::find($state);
                                        $set('valor_unitario', $procedimento?->valor_base ?? 0);
                                    }),
                                \Filament\Forms\Components\TextInput::make('quantidade')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live(),
                                \Filament\Forms\Components\TextInput::make('valor_unitario')
                                    ->label('Preço Unitário')
                                    ->numeric()
                                    ->prefix('R$')
                                    ->required(),
                            ])->columns(3)->columnSpanFull()
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
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendente' => 'danger',
                        'aprovado' => 'warning',
                        'pago' => 'success',
                    }),
                \Filament\Tables\Columns\TextColumn::make('created_at')
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
                // Botão para Gerar o PDF Profissional
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn(\App\Models\Orcamento $record): string => route('orcamento.pdf', $record))
                    ->openUrlInNewTab(),

                // Botão para enviar diretamente para o WhatsApp do Paciente
                Tables\Actions\Action::make('whatsapp')
                    ->label('Enviar Zap')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (\App\Models\Orcamento $record) {
                        // Calcula o total para colocar na mensagem
                        $total = number_format($record->itens->sum(fn($i) => $i->quantidade * $i->valor_unitario), 2, ',', '.');

                        // Texto da mensagem (URL Encoded)
                        $texto = urlencode("Olá " . $record->paciente->nome . "! Segue o orçamento da JR Odontologia no valor total de R$ " . $total . ". Caso queira aprovar, basta responder esta mensagem.");

                        // Limpa o número de telefone (remove parênteses e traços)
                        $telefone = preg_replace('/[^0-9]/', '', $record->paciente->telefone);

                        return "https://wa.me/55" . $telefone . "?text=" . $texto;
                    })
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
