<?php
return [
    'last_version_date' => \Carbon\Carbon::minValue(),
    'location' => [
        'out' => [
            'disk' => null,
            'file' => base_path('CHANGELOG.md')
        ],
        'in' => 'changelog'
    ]
];