<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskAttachmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);

    // Usuários
    Route::get('/colaboradores', [UserController::class, 'colaboradores']);
    Route::get('/gestores',      [UserController::class, 'gestores']);
    Route::get('/times',         [UserController::class, 'times']);
    Route::get('/usuarios',      [UserController::class, 'todos']);
    Route::post('/usuarios',     [UserController::class, 'store']); // Criar usuário — só gestor

    // Tarefas
    Route::get('/tasks',                     [TaskController::class, 'index']);
    Route::post('/tasks',                    [TaskController::class, 'store']);
    Route::get('/tasks/lixeira',             [TaskController::class, 'lixeira']);
    Route::post('/tasks/{id}/restaurar',     [TaskController::class, 'restaurar']);
    Route::get('/tasks/{task}',              [TaskController::class, 'show']);
    Route::put('/tasks/{task}',              [TaskController::class, 'update']);
    Route::delete('/tasks/{task}',           [TaskController::class, 'destroy']);

    // Comentários
    Route::get('/tasks/{task}/comments',     [TaskCommentController::class, 'index']);
    Route::post('/tasks/{task}/comments',    [TaskCommentController::class, 'store']);
    Route::delete('/comments/{comment}',     [TaskCommentController::class, 'destroy']);

    // Anexos
    Route::get('/tasks/{task}/attachments',          [TaskAttachmentController::class, 'index']);
    Route::post('/tasks/{task}/attachments',         [TaskAttachmentController::class, 'store']);
    Route::delete('/attachments/{attachment}',       [TaskAttachmentController::class, 'destroy']);
    Route::get('/attachments/{attachment}/download', [TaskAttachmentController::class, 'download']);

    // Perfil
    Route::get('/colaboradores/{user}',  [ProfileController::class, 'show']);
    Route::post('/perfil/avatar',        [ProfileController::class, 'uploadAvatar']);

    // Clientes
    Route::get('/clients',             [ClientController::class, 'index']);
    Route::post('/clients',            [ClientController::class, 'store']);
    Route::get('/clients/{client}',    [ClientController::class, 'show']);
    Route::put('/clients/{client}',    [ClientController::class, 'update']);
    Route::delete('/clients/{client}', [ClientController::class, 'destroy']);

});