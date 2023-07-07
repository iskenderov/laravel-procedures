<?php

namespace Iskenderov\Procedure;

use Illuminate\Support\ServiceProvider;
use Iskenderov\Procedure\commands\MakeProcedure;
use Iskenderov\Procedure\commands\RunProcedures;
use Iskenderov\Procedure\commands\WipeProcedures;

class ProcedureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeProcedure::class,
                RunProcedures::class,
                WipeProcedures::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
