# Laravel ChangeLog
A Laravel 5 package to create change-logs.

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

## Usage

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
