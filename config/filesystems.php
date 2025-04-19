<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'private'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [
        'private' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => public_path('storage'),
            'url' => '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'minio' => [
            'driver' => 's3',
            'key' => env('MINIO_ACCESS_KEY', 'aURFYTzo9PyjFitmRI2A'),
            'secret' => env('MINIO_SECRET_KEY', 'dy8RI1mZ3N4NgBA9fYTOZeOC88S9UWVePBshMX83'),
            'region' => env('MINIO_REGION', default: 'vn-middle-rack-01'),
            'bucket' => env('MINIO_DEFAULT_BUCKET', 'media'),
            'endpoint' => env('MINIO_ENDPOINT', 'https://minio.cvedix.com'),
            'use_path_style_endpoint' => true,
        ],
        'mqtt' => [
            'driver' => 'mqtt',
            'server' => env('MQTT_SERVER', 'server.aigova.com'),
            'port' => env('MQTT_PORT', 1883),
        ],
    ],


    'mediamtx' => [
        'url' => env('MEDIAMTX_SERVER', 'http://server.aigova.com:9997'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
