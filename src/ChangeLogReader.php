<?php
namespace Imarcom\ChangeLog\Commands;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Facades\Storage;

class GenerateChangeLogCommand extends BaseCommand
{
    /** @var string */
    protected $signature = 'changelog:generate';
    /** @var string */
    protected $description = 'Update the changelog file.';

    public function handle()
    {
       $changes = collect();
       /** @var Carbon $minimalDate */
       $minimalDate = config('changelog.last_version_date');
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
                            if(!$changes->contains($changeType)){
                                $changes->put($changeType,collect());
                            }
                            $changes[$changeType]->push($line);
                        }
                    }
                }
                fclose($handle);
            }
       }

       $disk = config('changelog.location.out.disk');
       $filename = config('changelog.location.out.file');
       $contents = view('laravel_changelog::changelog',['changes' => $changes]);
       if($disk){
           Storage::disk($disk)->put($filename,$contents);
       }else{
           file_put_contents($filename, $contents);
       }
    }

    protected function cleanLine($line){
        return trim(preg_replace('/\s+/', ' ', $line));
    }
}