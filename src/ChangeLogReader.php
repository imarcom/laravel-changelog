<?php
namespace Imarcom\ChangeLog;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ChangeLogReader
{
    protected $directories;

    protected $changes;

    /**
     * Get changes from changelogs
     *
     * @param Carbon|null $date Changelog before this date will be omitted. If not set, configuration value will be used.
     * @return Collection
     */
    public function getChanges(Carbon $date = null) : Collection{
        if(!$this->changes){
            $changes = collect();
            /** @var Carbon $minimalDate */
            $minimalDate = $date ?: config('changelog.last_version_date');
            foreach ($this->directories as $directory){
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
            }
            $this->changes = $changes;
        }
        return $this->changes;
    }

    /**
     * Add a directory from where to load changes.
     * @param String $directory
     */
    public function addDirectory(String $directory) : void{
        $this->directories[] = $directory;
    }

    protected function cleanLine(String $line) : String{
        return trim(preg_replace('/\s+/', ' ', $line));
    }
}