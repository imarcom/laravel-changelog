<?php
namespace Imarcom\ChangeLog;
use Illuminate\Support\ServiceProvider;
use Imarcom\ChangeLog\Commands\GenerateChangeLogCommand;
use Imarcom\ChangeLog\Commands\GetChangeLogCommand;

class ChangeLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/changelog.php' => config_path('changelog.php'),
        ], 'config');

        $this->commands([
            GenerateChangeLogCommand::class,
            GetChangeLogCommand::class
        ]);

        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel_changelog');
    }

    public function register()
    {

    }
}