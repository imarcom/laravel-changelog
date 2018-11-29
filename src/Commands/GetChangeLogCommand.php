<?php
namespace Imarcom\ChangeLog\Commands;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Storage;
use Imarcom\ChangeLog\ChangeLogReader;

class GenerateChangeLogCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'changelog:generate';
    /** @var string */
    protected $description = 'Update the changelog file.';

    protected $changeLogReader;

    public function __construct(ChangeLogReader $changeLogReader)
    {
        parent::__construct();
        $this->changeLogReader = $changeLogReader;
    }

    public function handle()
    {
       $changes = $this->changeLogReader->getChanges();
       $disk = config('changelog.location.out.disk');
       $filename = config('changelog.location.out.file');
       $contents = view('laravel_changelog::changelog',['changes' => $changes]);
       if($disk){
           Storage::disk($disk)->put($filename,$contents);
       }else{
           file_put_contents($filename, $contents);
       }
    }

}