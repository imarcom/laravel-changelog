<?php
namespace Spatie\Backup\Test\Integration;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Imarcom\ChangeLog\ChangeLogServiceProvider;
use Imarcom\ChangeLog\HasChangeLogs;
use Orchestra\Testbench\TestCase;

class GenerateChangeLogCommandTest extends TestCase
{
    use HasChangeLogs;

    protected function setUp()
    {
        parent::setUp();
        $this->addChangeLogsFrom(__DIR__.'/../files/changelog');
    }

    protected function getPackageProviders($app)
    {
        return [ChangeLogServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        Storage::fake('local');
        config([
            'changelog.releases' => [
                '1.0.0' => '2018-01-01',
                '1.0.1' => '2018-11-25'
            ],
            'changelog.location.out' => [
                'disk' => 'local',
                'file' => 'CHANGELOG.md'
            ]
        ]);
        parent::getEnvironmentSetUp($app);
    }


    /** @test */
    public function can_generate_changelog()
    {
        /** ACT */
        $resultCode = Artisan::call('changelog:generate');

        /** ASSERT */
        $this->assertEquals(0, $resultCode);

        $this->assertTrue(Storage::disk('local')->exists('CHANGELOG.md'));
        //dd(Storage::disk('local')->get('CHANGELOG.md'));
    }

    /** @test */
    public function can_get_changelog()
    {
        /** ACT */
        Artisan::call('changelog:get');

        /** ASSERT */
        $this->assertEquals("[Unreleased]\r
ADDED\r
- some new thing\r
\r
[1.0.1] - 2018-11-25\r
CHANGED\r
- some thing\r
- another thing\r
ADDED\r
- some thing\r
- some thing again\r
\r
[1.0.0] - 2018-01-01\r
ADDED\r
- some old thing\r
\r
",
        Artisan::output());
    }

    /** @test */
    public function can_get_changelog_with_tags()
    {
        /** ACT */
        Artisan::call('changelog:get',['--tags'=>'internal']);

        /** ASSERT */
        $this->assertEquals("[Unreleased]\r
ADDED\r
- some new thing\r
\r
[1.0.1] - 2018-11-25\r
CHANGED\r
- some thing\r
- another thing\r
- something secret \r
ADDED\r
- some thing\r
- some thing again\r
\r
[1.0.0] - 2018-01-01\r
ADDED\r
- some old thing\r
\r
",
            Artisan::output());
    }
}
