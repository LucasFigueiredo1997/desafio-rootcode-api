<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Lista todas as tasks do sistema
    // Gestor vê tudo, colaborador vê todas mas só edita as suas
    public function index(Request $request)
    {
        $tasks = Task::orderBy('due_date')->get();
        return response()->json($tasks, 200);
    }

    // Retorna tasks na lixeira (deletadas nas últimas 48hrs) — só gestor
    public function lixeira(Request $request)
    {
        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $tasks = Task::onlyTrashed()
                     ->where('deleted_at', '>=', now()->subHours(48))
                     ->with('deletedBy:id,name')
                     ->orderBy('deleted_at', 'desc')
                     ->get();

        return response()->json($tasks, 200);
    }

    // Cria uma nova tarefa — só gestor
    public function store(Request $request)
    {
        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Apenas gestores podem criar tarefas.'], 403);
        }

        $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'status'            => 'in:pendente,em_andamento,em_revisao,concluido',
            'difficulty'        => 'in:facil,medio,dificil',
            'due_date'          => 'nullable|date',
            'assigned_to'       => 'nullable|exists:users,id',
            'team_id'           => 'nullable|exists:teams,id',
            'client_id'         => 'nullable|exists:clients,id', // Cliente vinculado
            'reviewer_id'       => 'nullable|exists:users,id',
            'documentation_url' => 'nullable|url',
        ]);

        $task = Task::create([
            ...$request->all(),
            'user_id' => $request->user()->id, // Registra quem criou
        ]);

        return response()->json(['message' => 'Tarefa criada com sucesso!', 'task' => $task], 201);
    }

    // Exibe uma tarefa específica
    public function show(Request $request, Task $task)
    {
        return response()->json($task, 200);
    }

    // Atualiza uma tarefa
    // Gestor pode editar qualquer task
    // Colaborador só pode editar tasks onde é responsável
    public function update(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->role === 'colaborador' && $task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Você só pode editar tarefas em que é responsável.'], 403);
        }

        $request->validate([
            'title'             => 'sometimes|string|max:255',
            'description'       => 'nullable|string',
            'status'            => 'sometimes|in:pendente,em_andamento,em_revisao,concluido',
            'difficulty'        => 'sometimes|in:facil,medio,dificil',
            'due_date'          => 'nullable|date',
            'assigned_to'       => 'nullable|exists:users,id',
            'team_id'           => 'nullable|exists:teams,id',
            'client_id'         => 'nullable|exists:clients,id', 
            'reviewer_id'       => 'nullable|exists:users,id',
            'documentation_url' => 'nullable|url',
        ]);

        // Se o status está sendo alterado para "concluido", registra a data de conclusão
        $dados = $request->all();
        if (isset($dados['status'])) {
            if ($dados['status'] === 'concluido' && $task->status !== 'concluido') {
                $dados['completed_at'] = now(); // Registra o momento exato da conclusão
            } elseif ($dados['status'] !== 'concluido') {
                $dados['completed_at'] = null; // Se reabrir a task, limpa a data de conclusão
            }
        }

        $task->update($dados);

        return response()->json(['message' => 'Tarefa atualizada com sucesso!', 'task' => $task], 200);
    }

    // Deleta uma tarefa (soft delete) — só gestor
    public function destroy(Request $request, Task $task)
    {
        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Apenas gestores podem deletar tarefas.'], 403);
        }

        $task->update(['deleted_by' => $request->user()->id]);
        $task->delete(); // Soft delete — preenche deleted_at

        return response()->json(['message' => 'Tarefa movida para a lixeira.'], 200);
    }

    // Restaura uma task da lixeira — só gestor
    public function restaurar(Request $request, $id)
    {
        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $task = Task::onlyTrashed()->findOrFail($id);

        // Verifica se ainda está dentro das 48hrs
        if ($task->deleted_at->lt(now()->subHours(48))) {
            return response()->json(['message' => 'Prazo de restauração expirado.'], 422);
        }

        $task->restore();
        $task->update(['deleted_by' => null]);

        return response()->json(['message' => 'Tarefa restaurada com sucesso!', 'task' => $task], 200);
    }
}