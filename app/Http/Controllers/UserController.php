<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Lista todos os gestores — usado no select de "revisor" ao criar tarefa
    public function gestores()
    {
        $gestores = User::where('role', 'gestor')
                        ->select('id', 'name', 'email')
                        ->orderBy('name')
                        ->get();

        return response()->json($gestores, 200);
    }

    // Lista todos os colaboradores — usado no select de "responsável" ao criar tarefa
    public function colaboradores()
    {
        $colaboradores = User::where('role', 'colaborador')
                             ->select('id', 'name', 'email')
                             ->orderBy('name')
                             ->get();

        return response()->json($colaboradores, 200);
    }

    // Lista todos os times — usado no select de "time" ao criar tarefa
    public function times()
    {
        $times = Team::select('id', 'name')
                     ->orderBy('name')
                     ->get();

        return response()->json($times, 200);
    }

    // Lista todos os usuários com avatar — usado no select de "responsável" e nos cards do kanban
    public function todos()
    {
        $users = User::select('id', 'name', 'email', 'role', 'avatar')
                     ->orderBy('name')
                     ->get();

        // Converte o caminho do avatar para URL pública completa
        $users->transform(function ($user) {
            $user->avatar = $user->avatar ? asset('storage/' . $user->avatar) : null;
            return $user;
        });

        return response()->json($users, 200);
    }

    // Cria um novo usuário — só gestor
    public function store(Request $request)
    {
        \Log::info('Token recebido: ' . $request->bearerToken());
        \Log::info('Usuário: ' . json_encode($request->user()));

        if ($request->user()->role !== 'gestor') {
            return response()->json(['message' => 'Apenas gestores podem criar usuários.'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:gestor,colaborador',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
        ]);

        return response()->json(['message' => 'Usuário criado com sucesso!', 'user' => $user], 201);
    }
}