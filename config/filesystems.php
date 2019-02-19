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

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],
        'qiniu_public' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => 'pmzybetpt.bkt.clouddn.com', //你的七牛域名
                'https'     => '',         //你的HTTPS域名
                'custom'    => '',     //你的自定义域名
            ],
            'access_key'=> 'cvoy4ArV2yoc3MNLa_mMqa1SGXh5iwswNP7YkbeJ',  //AccessKey
            'secret_key'=> 'lTAwwWUgpcPMqcvBwAHBM60NqammAr3tt_bSuaLu',  //SecretKey
            'bucket'    => 'test',  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
        ],
        'qiniu_private' => [
            'driver'  => 'qiniu',
            'domains' => [
                'default'   => 'pm0mzqrq4.bkt.clouddn.com', //你的七牛域名
                'https'     => '',         //你的HTTPS域名
                'custom'    => '',     //你的自定义域名
            ],
            'access_key'=> 'cvoy4ArV2yoc3MNLa_mMqa1SGXh5iwswNP7YkbeJ',  //AccessKey
            'secret_key'=> 'lTAwwWUgpcPMqcvBwAHBM60NqammAr3tt_bSuaLu',  //SecretKey
            'bucket'    => 'laravel',  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
        ]

    ],

];
