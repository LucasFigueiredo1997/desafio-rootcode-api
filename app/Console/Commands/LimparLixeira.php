<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;

class LimparLixeira extends Command
{
    protected $signature = 'lixeira:limpar';
    protected $description = 'Deleta permanentemente tasks que estão na lixeira há mais de 48hrs';

    public function handle()
    {
        // Busca apenas tasks deletadas há MAIS de 48hrs
        $tasks = Task::onlyTrashed()
                     ->where('deleted_at', '<', now()->subHours(48))
                     ->get();

        $total = $tasks->count();

        foreach ($tasks as $task) {
            $task->forceDelete(); // forceDelete remove permanentemente do banco
        }

        $this->info("$total task(s) deletadas permanentemente.");
    }
}