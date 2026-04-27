<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Retorna o perfil de um colaborador com suas métricas de entregas
    public function show(User $user) 
    {
        $now = now();

        // Tasks onde o usuário é o RESPONSÁVEL (assigned_to)
        $todasTasks = \App\Models\Task::where('assigned_to', $user->id)->get();
        $tasksConcluidas = $todasTasks->where('status', 'concluido');

        $entregasSemana = $tasksConcluidas->filter(fn($t) => $t->updated_at->gte($now->copy()->startOfWeek()))->count();
        $entregasMes    = $tasksConcluidas->filter(fn($t) => $t->updated_at->gte($now->copy()->startOfMonth()))->count();
        $entregasAno    = $tasksConcluidas->filter(fn($t) => $t->updated_at->gte($now->copy()->startOfYear()))->count();

        $tasksAtivas = $todasTasks->where('status', '!=', 'concluido');
        $porDificuldade = [
            'facil'   => $tasksAtivas->where('difficulty', 'facil')->count(),
            'medio'   => $tasksAtivas->where('difficulty', 'medio')->count(),
            'dificil' => $tasksAtivas->where('difficulty', 'dificil')->count(),
        ];

        return response()->json([
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ],
            'metricas' => [
                'entregas_semana'  => $entregasSemana,
                'entregas_mes'     => $entregasMes,
                'entregas_ano'     => $entregasAno,
                'total_concluidas' => $tasksConcluidas->count(),
                'total_tasks'      => $todasTasks->count(),
                'por_dificuldade'  => $porDificuldade,
            ],
            'tasks_concluidas' => $tasksConcluidas->sortByDesc('updated_at')->values(),
            'todas_tasks'      => $todasTasks->sortBy('due_date')->values(),
        ]);
    }

    // Faz upload do avatar do usuário logado
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048', // Máximo 2MB, apenas imagens
        ]);

        $user = $request->user();

        // Remove o avatar antigo se existir
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Salva o novo avatar
        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Avatar atualizado com sucesso!',
            'avatar'  => asset('storage/' . $path),
        ]);
    }
}