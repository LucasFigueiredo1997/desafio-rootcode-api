<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens; // Importa o Sanctum para geração de tokens de autenticação

#[Fillable(['name', 'email', 'password', 'role', 'avatar'])] 
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable; // HasApiTokens habilita o Sanctum no model

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'password'                => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // Um usuário pode pertencer a várias equipes (através da tabela auxiliar team_user)
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')
                    ->withPivot('role_in_team')  // Traz também o campo role_in_team da tabela auxiliar
                    ->withTimestamps();
    }

    // Um usuário pode ter várias tarefas
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}