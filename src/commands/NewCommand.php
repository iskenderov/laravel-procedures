<?php

namespace Iskenderov\Procedure\commands;

use Illuminate\Console\Command;

class NewCommand extends Command
{
    public function handle()
    {
        $this->info('New command started...');

        //some code


        $this->info('New command finished...');
    }
}
