# Laravel ChangeLog
A Laravel 5 package to create markdown change-logs.

This allow to create individual changelog files which will be merged into a single file or even outputted in console or in application code for display.

## Installation

You can install this package via composer using this command:

```bash
composer require imarcom/laravel-changelog
```

The package will automatically register itself.

You can publish the config-file with:

```bash
php artisan vendor:publish --provider="Imarcom\LaravelChangeLog\ChangeLogServiceProvider"
```

### Configuration

```php
<?php
return [
    //By default, changelogs generated will only include changes after this date.
    'last_version_date' => \Carbon\Carbon::minValue(), 
    'location' => [
        //Where is the changelog file located
        'out' => [
            //You can specify a storage disk, if none is selected, it will but put locally at the set path.
            'disk' => null,
            //The path and name of the output changelog file. If a disk is selected, this will be retrived on the disk.
            'file' => base_path('CHANGELOG.md')
        ],
        //Changelog location directory for your project. This is relative to the project's base path.
        'in' => 'changelog'
    ]
];
```

### Using multiple changelog directories
You can register additional directories in providers by using the **Imarcom\ChangeLogs\HasChangeLogs** trait.
```php
<?php
class MyServiceProvider extends ServiceProvider
{
    use \Imarcom\ChangeLog\HasChangeLogs;

    public function boot()
    {
        //...
        $this->addChangeLogsFrom('my/custom/directory');
    }
}
```
While developing packages, this allow to use a directory for your package which will be used upon registration of the provider.


## Usage
### Writing Change-logs
When you make changes to your application. Add a file into your **changelog** directory as configured. This file should be named with this pattern : **YYYYMMDD_some_file_name.md**

Write it like a standalone change-log like :

```markdown
## CHANGED
- some thing
- another thing

## ADDED
- some thing
```

### Creating ChangeLogs
When creating change-logs, all change-logs from registered directories will be merged together into a single change-log by regrouping lines under the same headers together.

Generating the changeLog file.

```bash
php artisan changelog:generate
```

Reading changelog from console without saving it to file.

```bash
php artisan changelog:get
```

Retrieving changes as array.

```php
<?php
$changes = app(\Imarcom\ChangeLog\ChangeLogReader::class)->getChanges();
```

## Testing

You can run the tests with:

```bash
vendor/bin/phpunit
```
