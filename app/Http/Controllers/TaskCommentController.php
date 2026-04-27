<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;

class TaskCommentController extends Controller
{
    // Lista todos os comentários de uma tarefa
    public function index(Request $request, Task $task)
    {
        // Garante que o usuário só pode ver comentários de suas próprias tarefas
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $comments = $task->comments()
                         ->with('user:id,name,role') // Carrega nome e role do autor
                         ->orderBy('created_at', 'asc')
                         ->get();

        return response()->json($comments, 200);
    }

    // Adiciona um comentário a uma tarefa
    public function store(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        // Retorna o comentário com os dados do autor
        $comment->load('user:id,name,role');

        return response()->json($comment, 201);
    }

    // Deleta um comentário
    public function destroy(Request $request, TaskComment $comment)
    {
        // Só o autor pode deletar seu próprio comentário
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentário deletado com sucesso!'], 200);
    }
}