<?php

return [
    /**
     * Configuration keys to be shared.
     * 
     * Example:
     * 'app' shares config('app')
     * 'app.name' shares config('app.name')
     */
    'configs' => [
        'app.name',
    ],

    /**
     * Define flash variables to be shared
     */
    'flash' => [
        'info',
        'error',
        'alert',
        'success',
    ]
];