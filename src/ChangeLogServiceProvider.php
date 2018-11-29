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
        $this->publishes([
            __DIR__.'/../config/changelog.php' => config_path('changelog.php'),
        ], 'config');

        $this->commands([
            GenerateChangeLogCommand::class,
            GetChangeLogCommand::class
        ]);

        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel_changelog');
        $this->addChangeLogsFrom(config('changelog.location.in'));
    }

    public function register()
    {
    }
}