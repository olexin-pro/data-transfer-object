<?php

declare(strict_types=1);

namespace Ol3x1n\DataTransferObject;

use Illuminate\Support\ServiceProvider;

final class DTOProvider extends ServiceProvider
{
    public function register(): void
    {
        //$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

         $this->mergeConfigFrom(
             __DIR__ . '/../config/dto.php',
             'dto'
         );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

             $this->publishes([
                 __DIR__ . '/../config/dto.php' => config_path('dto.php'),
             ], 'dto-config');

//
//            $this->publishes([
//                __DIR__ . '/../database/migrations' => database_path('migrations'),
//            ], 'pipeline-migrations');
        }
    }
}