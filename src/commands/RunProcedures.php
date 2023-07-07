<?php

namespace Iskenderov\Procedure\commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RunProcedures extends Command
{
    public const PATH = "procedures";

    protected $signature = 'proceed {name? : Procedure name}';

    protected $description = 'Run procedure by name or execute all existing procedures';

    public function handle(): void
    {
        if ($this->argument('name') === null) {
            $this->processDirectory();
            exit();
        }

        $this->processSingleProcedure();
    }

    private function processDirectory(): void
    {
        $files = File::files(database_path(self::PATH));

        foreach ($files as $file) {
            $this->name = Str::studly($file->getFilenameWithoutExtension());
            if ($this->checkProceedTable()) {
                $this->runProcedure();
            }
        }

        $this->info('All procedures run successfully!');
    }

    private function processSingleProcedure(): void
    {
        $this->name = Str::studly($this->argument('name'));

        $this->checkIfExist();

        if ($this->checkProceedTable()) {
            $this->runProcedure();
        }

        $this->info('Procedure run successfully!');
    }

    private function checkProceedTable(): bool
    {
        $checkSum = md5_file(database_path(self::PATH . "/{$this->name}.sql"));

        $exists = DB::table('proceed')
            ->where('name', $this->name)
            ->where('checksum', $checkSum)
            ->exists();

        if ($exists) {
            $this->warn("Procedure {$this->name} already exists!");
            return false;
        }

        DB::table('proceed')->updateOrInsert(
            ['name' => $this->name],
            ['checksum' => $checkSum]
        );

        return true;
    }

    private function runProcedure(): void
    {
        $query = File::get(database_path(self::PATH . "/{$this->name}.sql"));

        DB::unprepared($query);

        $this->info("Procedure {$this->name} run successfully!");
    }

    private function checkIfExist(): void
    {
        if (!File::exists(database_path(self::PATH . "/{$this->name}.sql"))) {
            $this->warn("Procedure '{$this->name}' doesnt exists!");
            exit();
        }
    }

}
