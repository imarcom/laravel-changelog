<?php
namespace Imarcom\ChangeLog;

use Carbon\Carbon;
use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ChangeLogReader
{
    protected $directories = [];

    protected $changes;

    /**
     * Get changes from changelogs
     *
     * @param array $tags         tagged lines to show
     * @param bool  $skipUntagged if the untagged lines should be shown
     * @param bool  $allTags      display all tags
     * @param bool  $showTags     display tags on tagges lines
     * @param bool  $annotate     show file name information next to lines.
     * @return Collection
     */
    public function getChanges(
        array $tags = [],
        bool $skipUntagged = false,
        bool $allTags = false,
        bool $showTags = false,
        bool $annotate = false
) : Collection{
        if(!$this->changes){
            $changesByRelease = collect([
                'unreleased' => [
                    'date' => null,
                    'changes' => collect()
                ]
            ]);
            $releaseDates = config('changelog.releases',[]);
            foreach ($this->directories as $directory){
                foreach(scandir($directory) as $file){
                    if($file !== '.' && $file !== '..'){
                        $handle = fopen($directory.DIRECTORY_SEPARATOR.$file, 'r');
                        preg_match('/\d{8}_(.+)\.md/', $file,$annotationMatches);
                        if($handle){
                            $release = $this->getRelease(Carbon::parse(substr($file, 0,8)));
                            $changeType = '';
                            while (($line = fgets($handle)) !== false){
                                $line = $this->cleanLine($line);
                                if(starts_with($line, ['#'])) {
                                    $changeType = strtolower(trim($line, '# '));
                                }
                                else if($line){
                                    preg_match_all('/\[(.+)]$/', $line, $matches, PREG_SET_ORDER, 0);
                                    if($matches){
                                        if(!$allTags && !in_array($matches[0][1], $tags)){
                                            continue; //Skip tagged lines which are not requested.
                                        }
                                        if(!$showTags){
                                            $line = preg_replace('/\[.+]$/', '', $line); //hide tags
                                        }
                                    }elseif ($skipUntagged){
                                        continue; //Skip untagged lines if requested.
                                    }
                                    if(!$changesByRelease->has($release)){
                                        $changesByRelease->put($release,[
                                            'date' => array_get($releaseDates,$release),
                                            'changes' => collect()
                                        ]);
                                    }
                                    if(!$changesByRelease[$release]['changes']->has($changeType)){
                                        $changesByRelease[$release]['changes']->put($changeType,collect());
                                    }
                                    if($annotate && $annotationMatches){
                                        $line .=' ['.$annotationMatches[1].']';
                                    }
                                    $changesByRelease[$release]['changes'][$changeType]->push($line);
                                }
                            }
                        }
                        fclose($handle);
                    }
                }
            }
            $changesByRelease = $changesByRelease->sortBy(function($releaseInfo,$release){
                return $release === 'unreleased' ? Carbon::maxValue() : Carbon::parse($releaseInfo['date']);
            })->reverse();

            //Use a message instead of changes for first release if configured so.
            if($releaseDates && !config('changelog.first_version.display_changes',true)){
                $firstReleaseVersion = head(array_keys($releaseDates));
                $firstRelease = $changesByRelease[$firstReleaseVersion];
                $firstRelease['changes'] = [
                    '' => [
                        config('changelog.first_version.message','- First release')
                    ]
                ];
                $changesByRelease[$firstReleaseVersion] = $firstRelease;
            }

            //Remove empty unreleased tag.
            if($changesByRelease['unreleased']['changes']->isEmpty()){
                unset($changesByRelease['unreleased']);
            }

            $this->changes = $changesByRelease;
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

    protected function getRelease(Carbon $changeDate) : String{
        $releases = collect(config('changelog.releases',[]))->map(function ($date) {
            return Carbon::parse($date);
        })->sort();
        $changeRelease = 'unreleased';
        foreach ($releases as $release => $releaseDate){
            if($changeDate->lessThanOrEqualTo($releaseDate)){
                $changeRelease = $release;
                break;
            }
        }
        return $changeRelease;
    }
}