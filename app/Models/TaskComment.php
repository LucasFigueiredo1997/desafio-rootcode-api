<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'user_id', 'content'];

    // O comentário pertence a uma tarefa
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // O comentário pertence a um usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}