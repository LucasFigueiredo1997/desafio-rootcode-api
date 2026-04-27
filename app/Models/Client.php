<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'email', 'phone', 'document', 'notes', 'segment',
    ];

    // Um cliente pode ter várias tasks
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}