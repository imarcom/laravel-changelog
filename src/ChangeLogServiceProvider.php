<?php
namespace Imarcom\ChangeLog;
use Illuminate\Support\ServiceProvider;
use Imarcom\ChangeLog\Commands\GenerateChangeLogCommand;
use Imarcom\ChangeLog\Commands\GetChangeLogCommand;

class ChangeLogServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(ChangeLogReader::class);

        $configPath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'changelog.php';
        $this->publishes([$configPath => config_path('changelog.php')]);


        $this->commands([
            GenerateChangeLogCommand::class,
            GetChangeLogCommand::class
        ]);

        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel_changelog');
    }

    public function register()
    {
        $configPath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'changelog.php';
        $this->mergeConfigFrom($configPath, 'changelog');
    }
}