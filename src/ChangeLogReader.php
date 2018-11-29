<?php
namespace Imarcom\ChangeLog;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Storage;

class ChangeLogReader
{
    public function getChanges(Carbon $date = null){
        $changes = collect();
        /** @var Carbon $minimalDate */
        $minimalDate = $date ?: config('changelog.last_version_date');
        $directory = config('changelog.location.in');
        foreach(scandir($directory) as $file){
            $date = Carbon::parse(substr($file, 0,'8'));
            if($file !== '.' && $file !== '..' && $minimalDate->lt($date)){
                $handle = fopen($directory.DIRECTORY_SEPARATOR.$file, 'r');
                if($handle){
                    $changeType = '';
                    while (($line = fgets($handle)) !== false){
                        $line = $this->cleanLine($line);
                        if(starts_with($line, ['#'])){
                            $changeType = strtolower(trim($line,'# '));
                        }else if($line){
                            if(!$changes->has($changeType)){
                                $changes->put($changeType,collect());
                            }
                            $changes[$changeType]->push($line);
                        }
                    }
                }
                fclose($handle);
            }
        }
        return $changes;
    }

    protected function cleanLine($line){
        return trim(preg_replace('/\s+/', ' ', $line));
    }
}