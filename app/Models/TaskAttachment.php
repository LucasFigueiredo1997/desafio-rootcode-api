<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'user_id', 'filename', 'path', 'mime_type', 'size'];

    // O anexo pertence a uma tarefa
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // O anexo pertence a um usuário (quem fez upload)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}