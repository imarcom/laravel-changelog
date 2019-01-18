# Laravel ChangeLog
A Laravel 5 package to create markdown change-logs.

This allow to create individual changelog files which will be merged into a single file or even outputted in console or in application code for display.

The outputted markdown file will look like this:

```markdown
# ChangeLog

## [Unreleased]
### ADDED
- some new thing

##[1.0.1] - 2018-11-25
### CHANGED
- some thing
- another thing
### ADDED
- some thing
- some thing again

## [1.0.0] - 2018-01-01
### ADDED
- some old thing
```

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
    //This allow to classify changelogs by release date. Everything after the last version will be unreleased.
    'versions' => [
        '1.0.0' => '2019-01-01',
        '1.0.1' => '2019-03-08'    
    ], 
    'location' => [
        //Where is the changelog file located
        'out' => [
            //You can specify a storage disk, if none is selected, it will but put locally at the set path.
            'disk' => null,
            //The path and name of the output changelog file. If a disk is selected, this will be retrived on the disk.
            'file' => base_path('CHANGELOG.md')
        ]
    ]
];
```

### Registering changelog directories
You can register directories in providers by using the **Imarcom\ChangeLogs\HasChangeLogs** trait.
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
When you make changes to your application. Add a file into your **changelog** directory. This file should be named with this pattern : **YYYYMMDD_some_file_name.md**

Write it like a standalone change-log like :

```markdown
## CHANGED
- some thing
- another thing

## ADDED
- some thing
```

#### Hiding content with tags
You may want to add some lines which should not be always visible. For this, you can add *[TAG]* at the end of the line. This line will not be output when generating changelogs if the tag has not been requested.
```markdown
- Something for devs only [dev_only]
```

### Creating ChangeLogs
When creating change-logs, all change-logs from registered directories will be merged together into a single change-log by regrouping lines under the same headers together.

Generating the changeLog file.

```bash
php artisan changelog:generate
```
with tags
```bash
php artisan changelog:generate --tags=tag1,tag2,tag3
```


Reading changelog from console without saving it to file.

```bash
php artisan changelog:get
```
with tags
```bash
php artisan changelog:get --tags=tag1,tag2,tag3
```


Retrieving changes as array.

```php
<?php
$changes = app(\Imarcom\ChangeLog\ChangeLogReader::class)->getChanges();
```
with tags
```php
<?php
$changes = app(\Imarcom\ChangeLog\ChangeLogReader::class)->getChanges(['tag1','tag2','tag3']);
```

## Testing

You can run the tests with:

```bash
vendor/bin/phpunit
```
