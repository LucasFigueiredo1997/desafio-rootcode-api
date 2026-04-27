<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
{
    // Lista todos os anexos de uma tarefa
    public function index(Request $request, Task $task)
    {
        $attachments = $task->attachments()
                            ->with('user:id,name')
                            ->orderBy('created_at', 'asc')
                            ->get();

        return response()->json($attachments, 200);
    }

    // Faz upload de um anexo
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = TaskAttachment::create([
            'task_id'   => $task->id,
            'user_id'   => $request->user()->id,
            'filename'  => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
        ]);

        $attachment->load('user:id,name');

        return response()->json($attachment, 201);
    }

    // Deleta um anexo
    public function destroy(Request $request, TaskAttachment $attachment)
    {
        if ($attachment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        // Remove o arquivo do storage
        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return response()->json(['message' => 'Anexo deletado com sucesso!'], 200);
    }

    // Download de um anexo
    public function download(Request $request, TaskAttachment $attachment)
    {
        if ($attachment->task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        return Storage::disk('public')->download($attachment->path, $attachment->filename);
    }
}