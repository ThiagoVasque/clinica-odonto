<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PacienteResource\Pages;
use App\Models\Paciente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Ícone de usuários combina mais

    // Nomes corrigidos para a Sidebar
    protected static ?string $modelLabel = 'Paciente';
    protected static ?string $pluralModelLabel = 'Pacientes';
    protected static ?string $navigationGroup = 'Atendimento';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dados Pessoais')
                    ->description('Informações básicas do paciente')
                    ->schema([
                        Forms\Components\TextInput::make('nome')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->unique(table: 'pacientes', ignoreRecord: true)
                            ->required(),
                        Forms\Components\DatePicker::make('data_nascimento')
                            ->label('Data de Nascimento')
                            ->displayFormat('d/m/Y')
                            ->native(true) 
                            ->placeholder('__/__/____')
                            ->maxDate(now()), 
                        Forms\Components\TextInput::make('telefone')
                            ->label('WhatsApp/Telefone')
                            ->tel()
                            ->mask('(99) 99999-9999'),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email(),
                        Forms\Components\Textarea::make('historico_medico')
                            ->label('Histórico Médico (Anamnese)')
                            ->placeholder('Alergias, medicamentos em uso, cirurgias prévias...')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Nome do Paciente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefone')
                    ->label('WhatsApp/Celular'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastrado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // AGORA DENTRO DO DROPDOWN (ActionGroup)
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->label('Editar Registro'),

                    Tables\Actions\Action::make('whatsapp')
                        ->label('Chamar no Zap')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->url(function (Paciente $record) {
                            if (!$record->telefone) return null;
                            $telefone = preg_replace('/\D/', '', $record->telefone);
                            return "https://wa.me/55{$telefone}";
                        })
                        ->openUrlInNewTab(),

                    Tables\Actions\DeleteAction::make()
                        ->label('Excluir Paciente'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical') // Ícone de 3 pontinhos
                    ->tooltip('Opções')
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
            'index' => Pages\ListPacientes::route('/'),
            'create' => Pages\CreatePaciente::route('/create'),
            'edit' => Pages\EditPaciente::route('/{record}/edit'),
        ];
    }
}
