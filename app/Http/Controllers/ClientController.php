<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Task;

class ClientController extends Controller
{
    // Lista todos os clientes
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        return response()->json($clients, 200);
    }

    // Cria um novo cliente — só gestor
    public function store(Request $request)
    {
        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'type'     => 'in:empresa,pessoa_fisica',
            'email'    => 'nullable|email',
            'phone'    => 'nullable|string',
            'document' => 'nullable|string',
            'notes'    => 'nullable|string',
            'segment'  => 'in:corporativo,lazer,grupos,lua_de_mel,aventura,cruzeiros',
        ]);

        $client = Client::create($request->all());
        return response()->json($client, 201);
    }

    // Exibe um cliente específico com suas tasks e métricas
    public function show(Client $client)
    {
        $tasks = $client->tasks()->get();
        $concluidas = $tasks->where('status', 'concluido');

        $now = now();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();
        $startOfYear = now()->startOfYear();

        return response()->json([
            'client' => $client,
            'metricas' => [
                'total_tasks'      => $tasks->count(),
                'total_concluidas' => $concluidas->count(),
                'entregas_semana'  => $concluidas->filter(fn($t) => $t->updated_at->gte($startOfWeek))->count(),
                'entregas_mes'     => $concluidas->filter(fn($t) => $t->updated_at->gte($startOfMonth))->count(),
                'entregas_ano'     => $concluidas->filter(fn($t) => $t->updated_at->gte($startOfYear))->count(),
                'por_dificuldade'  => [
                    'facil'   => $tasks->where('status', '!=', 'concluido')->where('difficulty', 'facil')->count(),
                    'medio'   => $tasks->where('status', '!=', 'concluido')->where('difficulty', 'medio')->count(),
                    'dificil' => $tasks->where('status', '!=', 'concluido')->where('difficulty', 'dificil')->count(),
                ],
            ],
            'todas_tasks'      => $tasks->sortBy('due_date')->values(),
            'tasks_concluidas' => $concluidas->sortByDesc('updated_at')->values(),
        ]);
    }

    // Atualiza um cliente — só gestor
    public function update(Request $request, Client $client)
    {
        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'type'     => 'sometimes|in:empresa,pessoa_fisica',
            'email'    => 'nullable|email',
            'phone'    => 'nullable|string',
            'document' => 'nullable|string',
            'notes'    => 'nullable|string',
            'segment'  => 'sometimes|in:corporativo,lazer,grupos,lua_de_mel,aventura,cruzeiros',
        ]);

        $client->update($request->all());
        return response()->json($client, 200);
    }

    // Deleta um cliente — só gestor
    public function destroy(Request $request, Client $client)
    {
        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $client->delete();
        return response()->json(['message' => 'Cliente deletado com sucesso!'], 200);
    }
}