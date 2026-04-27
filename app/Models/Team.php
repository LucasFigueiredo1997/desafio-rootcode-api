<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['name'];

    // Uma equipe tem muitos usuários (através da tabela auxiliar team_user)
    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user')
                    ->withPivot('role_in_team')  // Traz também o campo role_in_team da tabela auxiliar
                    ->withTimestamps();
    }

    // Atalho para pegar apenas os gestores da equipe
    public function gestores()
    {
        return $this->users()->wherePivot('role_in_team', 'gestor');
    }

    // Atalho para pegar apenas os colaboradores da equipe
    public function colaboradores()
    {
        return $this->users()->wherePivot('role_in_team', 'colaborador');
    }
}