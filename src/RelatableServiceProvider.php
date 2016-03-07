<?php

namespace Spatie\Relatable;

use Illuminate\Support\ServiceProvider;

class RelatableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-relatable.php' => config_path('laravel-relatable.php'),
        ], 'config');

        if (!class_exists('CreateRelatablesTable')) {

            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/create_relatables_table.php.stub' =>
                    database_path('migrations/'.$timestamp.'_create_relatables_table.php'),
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-relatable.php', 'laravel-relatable');
    }
}
