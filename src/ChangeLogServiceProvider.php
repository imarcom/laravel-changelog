<?php
namespace Imarcom\ChangeLog;
use Illuminate\Support\ServiceProvider;
use Imarcom\ChangeLog\Commands\GenerateChangeLogCommand;
use Imarcom\ChangeLog\Commands\GetChangeLogCommand;

class ChangeLogServiceProvider extends ServiceProvider
{
    use HasChangeLogs;

    public function boot()
    {
        $this->app->singleton(ChangeLogReader::class);

        $configPath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'changelog.php';
        $this->publishes([$configPath => config_path('changelog.php')]);
        $this->mergeConfigFrom($configPath, 'changelog');

        $this->commands([
            GenerateChangeLogCommand::class,
            GetChangeLogCommand::class
        ]);

        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel_changelog');
        $this->addChangeLogsFrom(config('changelog.location.in','changelog'));
    }

    public function register()
    {
    }
}