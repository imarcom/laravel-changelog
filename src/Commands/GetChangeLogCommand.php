<?php
namespace Imarcom\ChangeLog\Commands;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Storage;
use Imarcom\ChangeLog\ChangeLogReader;

class GetChangeLogCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'changelog:get {--tags=} {--t|tagged} {--at|alltags} {--st|showtags} {--an|annotate}';
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
        $changes = $this->changeLogReader->getChanges(
            explode(',',$this->option('tags'))?:[],
            $this->option('tagged'),
            $this->option('alltags'),
            $this->option('showtags'),
            $this->option('annotate')
        );
        foreach ($changes as $release => $releaseInfo){
            $this->line('['.ucfirst($release).']'.($releaseInfo['date']?' - '.$releaseInfo['date']:''));
            foreach ($releaseInfo['changes'] as $type => $changes){
                $this->line(strtoupper($type));
                foreach ($changes as $change){
                    $this->line($change);
                }
            }
            $this->line('');
        }
    }

}