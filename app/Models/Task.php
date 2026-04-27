<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes; // SoftDeletes habilita o deleted_at automático

    protected $fillable = [
        'user_id',
        'assigned_to',
        'client_id',
        'team_id',
        'reviewer_id',
        'deleted_by',
        'documentation_url',
        'title',
        'description',
        'status',
        'difficulty',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date'   => 'date',
            'deleted_at' => 'datetime', // Garante que deleted_at vem como objeto de data
            'completed_at' => 'datetime', // Garante que vem como objeto de data com horário
        ];
    }

    // Uma tarefa pertence a um usuário (criador)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Colaborador responsável pela tarefa
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // A task pertence a um cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Time responsável pela tarefa
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    // Responsável pela revisão
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Quem deletou a tarefa
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Uma tarefa pode ter vários comentários
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    // Uma tarefa pode ter vários anexos
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}