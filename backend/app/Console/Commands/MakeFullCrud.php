<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFullCrud extends Command
{
    protected $signature = 'make:fullcrud {name}';
    protected $description = 'Cria Model, Migration, Controller, Request e Resource para um CRUD básico';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $plural = Str::pluralStudly($name);

        // Criar Model com Migration
        $this->call('make:model', [
            'name' => $name,
            '--migration' => true,
        ]);

        // Criar Controller API
        $this->call('make:controller', [
            'name' => "Api/{$name}Controller",
            '--api' => true,
        ]);

        // Criar FormRequest
        $this->call('make:request', [
            'name' => "{$name}Request",
        ]);

        // Criar Resource
        $this->call('make:resource', [
            'name' => "{$name}Resource",
        ]);
        
        $this->info("CRUD completo gerado para: {$name}");
    }
}
//php artisan make:fullcrud Produto