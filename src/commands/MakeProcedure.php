<?php

namespace Iskenderov\Procedure\commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeProcedure extends Command
{
    public const PATH = "procedures";

    protected $signature = 'make:procedure {name : Procedure name}';

    protected $description = 'Create a new procedure';

    protected $name;

    public function handle(): void
    {
        if ($this->argument('name') === null) {
            $this->warn('Please provide a procedure name...');
            exit();
        }

        $this->name = Str::studly($this->argument('name'));

        $this->checkIfExist();

        $this->createProcedure();

        $this->info('Procedure created successfully.');
    }

    private function checkIfExist(): void
    {
        if (File::exists(database_path(self::PATH . "/{$this->name}.sql"))) {
            $this->warn('Procedure already exists!');
            exit();
        }
    }

    private function createProcedure(): void
    {
        File::ensureDirectoryExists(database_path('procedures'));

        $stub = File::get(__DIR__ . '/../stubs/procedure.stub');

        $stub = str_replace('{{ procedure_name }}', $this->name, $stub);

        File::put(database_path(self::PATH . "/{$this->name}.sql"), $stub);
    }
}
