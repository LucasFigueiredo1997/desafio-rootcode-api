<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // Rota de login — recebe email e senha, retorna um token
    public function login(Request $request)
    {
        // Valida se os campos foram enviados corretamente
        $request->validate([
            'email'    => 'required|email',    // Campo obrigatório e deve ser um email válido
            'password' => 'required|string',   // Campo obrigatório e deve ser uma string
        ]);

        // Tenta autenticar o usuário com as credenciais enviadas
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciais inválidas.'
            ], 401); // 401 = Não autorizado
        }

        // Busca o usuário autenticado
        $user = Auth::user();

        // Apaga tokens antigos para evitar acúmulo
        $user->tokens()->delete();

        // Gera um novo token de acesso via Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Login realizado com sucesso!',
            'token'        => $token,   // Token que o frontend vai guardar e usar nas requisições
            'user'         => $user,
        ], 200);
    }

    // Rota de logout — invalida o token atual
    public function logout(Request $request)
    {
        // Deleta apenas o token usado nessa requisição
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!'
        ], 200);
    }
}