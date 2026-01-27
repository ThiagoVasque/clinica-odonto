<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser; // IMPORTANTE
use Filament\Panel; // IMPORTANTE
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser // IMPORTANTE: Adicionado implements
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // IMPORTANTE: Este método é o que mata o erro 403
    public function canAccessPanel(Panel $panel): bool
    {
        // Aqui estamos liberando para qualquer usuário cadastrado logar.
        return true; 
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}