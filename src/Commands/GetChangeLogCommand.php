<?php
namespace Imarcom\ChangeLog\Commands;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Storage;
use Imarcom\ChangeLog\ChangeLogReader;

class GetChangeLogCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'changelog:get';
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
        foreach ($changes as $type => $typeChanges){
            $this->line(strtoupper($type));
            foreach ($typeChanges as $change){
                $this->line($change);
            }
        }
    }

}