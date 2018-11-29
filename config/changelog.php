<?php
return [
    'last_version_date' => \Carbon\Carbon::minValue(),
    'location' => [
        'out' => [
            'disk' => null,
            'file' => 'CHANGELOG.md'
        ],
        'in' => 'changelog'
    ]
];