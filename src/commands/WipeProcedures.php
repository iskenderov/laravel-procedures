<?php

namespace Iskenderov\Procedure\commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WipeProcedures extends Command
{
    protected $signature = 'proceed:wipe';

    protected $description = 'Truncate proceed table and drop all procedures';

    public function handle(): void
    {
        $this->warn('This command will drop all procedures and truncate proceed table!');

        if (!$this->confirm('Do you wish to continue?')) {
            exit();
        }

        $this->truncateProceedTable();

        $this->wipeProcedure();

        $this->info('All procedures wiped successfully!');
    }

    private function wipeProcedure(): void
    {
        $database = DB::connection()->getDatabaseName();

        $query = "SELECT SPECIFIC_NAME AS NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE = 'PROCEDURE' AND ROUTINE_SCHEMA = '{$database}'";

        $result = DB::select($query);

        foreach ($result as $procedure) {
            $query = "DROP PROCEDURE IF EXISTS {$procedure->NAME}";
            DB::statement($query);
        }
    }

    private function truncateProceedTable()
    {
        DB::table('proceed')->truncate();
    }
}
