<?php
namespace Spatie\Backup\Test\Integration;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Imarcom\ChangeLog\ChangeLogServiceProvider;
use Orchestra\Testbench\TestCase;

class BackupCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ChangeLogServiceProvider::class];
    }

    /** @test */
    public function can_generate_changelog()
    {
        config([
            'changelog.last_version_date' => Carbon::minValue(),
            'changelog.location.in' => __DIR__.'/../files/changelog',
            'changelog.location.out' => [
                'disk' => 'local',
                'file' => 'CHANGELOG.md'
            ]
        ]);

        Storage::fake('local');

        $resultCode = Artisan::call('changelog:generate');
        $this->assertEquals(0, $resultCode);

        $this->assertTrue(Storage::disk('local')->exists('CHANGELOG.md'));
    }

    /** @test */
    public function can_get_changelog()
    {
        config([
            'changelog.last_version_date' => Carbon::minValue(),
            'changelog.location.in' => __DIR__.'/../files/changelog',
            'changelog.location.out' => [
                'disk' => 'local',
                'file' => 'CHANGELOG.md'
            ]
        ]);

        Storage::fake('local');

        Artisan::call('changelog:get');
        $this->assertEquals("CHANGED\r
- some thing\r
- another thing\r
ADDED\r
- some thing\r
- some thing again\r
",
        Artisan::output());
    }
}