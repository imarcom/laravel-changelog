<?php
namespace Imarcom\ChangeLog;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Storage;

trait HasChangeLogs
{
    protected function addChangeLogsFrom(String $directory) : void {
        app(ChangeLogReader::class)->addDirectory($directory);
    }
}