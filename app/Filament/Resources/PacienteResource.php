<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacienteResource\Pages;
use App\Filament\Resources\PacienteResource\RelationManagers;
use App\Models\Paciente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // app/Filament/Resources/PacienteResource.php

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Dados Pessoais')
                    ->description('Informações básicas do paciente')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('nome')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            // Ajustado para evitar o erro visual do VS Code
                            ->unique(table: 'pacientes', ignoreRecord: true)
                            ->required(),
                        \Filament\Forms\Components\DatePicker::make('data_nascimento')
                            ->label('Data de Nascimento')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        \Filament\Forms\Components\TextInput::make('telefone')
                            ->label('WhatsApp/Telefone')
                            ->tel()
                            ->mask('(99) 99999-9999'),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email(),
                        \Filament\Forms\Components\Textarea::make('historico_medico')
                            ->label('Histórico Médico (Anamnese)')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('nome')
                    ->label('Nome do Paciente')
                    ->searchable() // Cria uma barra de busca só para nomes
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('telefone')
                    ->label('WhatsApp/Celular'),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Por enquanto deixaremos vazio, mas aqui poderíamos filtrar por data, etc.
            ])
            ->actions([
                Tables\Actions\EditAction::make(), // Botão de Lápis para editar
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Permite apagar vários de uma vez
                ]),
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
            'index' => Pages\ListPacientes::route('/'),
            'create' => Pages\CreatePaciente::route('/create'),
            'edit' => Pages\EditPaciente::route('/{record}/edit'),
        ];
    }
}
