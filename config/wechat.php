<?php

/*
 * This file is part of the overtrue/laravel-wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * 默认配置，将会合并到各模块中
     */
    'defaults' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
         */
        'response_type' => 'array',

        /*
         * 使用 Laravel 的缓存系统
         */
        'use_laravel_cache' => true,

        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'file' => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
        ],
    ],

    /*
     * 路由配置
     */
    'route' => [
        /*
         * 开放平台第三方平台路由配置
         */
        // 'open_platform' => [
        //     'uri' => 'serve',
        //     'action' => Overtrue\LaravelWeChat\Controllers\OpenPlatformController::class,
        //     'attributes' => [
        //         'prefix' => 'open-platform',
        //         'middleware' => null,
        //     ],
        // ],
    ],

    /*
     * http://tg.hlbapp.com/wechat
     * 公众号
     */
    'official_account' => [
        'default' => [
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', 'wx8bc33962b0a8ecef'),         // AppID
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', '9db21c90c52ab7c7575d3d45de2b6bc6'),    // AppSecret
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', '24C8150D747ED0708AF7063031D99093'),           // Token
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', 'IIURTZsVOZL8IghihMWop52jqVaO7c8s79B3j4C1IXj'),                 // EncodingAESKey
            'response_type' =>  'array',
            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            // 'oauth' => [
            //     'scopes'   => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
            //     'callback' => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/examples/oauth_callback.php'),
            // ],
        ],
    ],

    /*
     * 开放平台第三方平台
     */
//     'open_platform' => [
//         'default' => [
//             'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
//             'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
//             'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
//             'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
//         ],
//     ],

    /*
     * 小程序
     */
     'mini_program' => [
         'default' => [
             'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', 'wx68f2dc67c99f4b05'),
             'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', 'a5be48748215affac4fb2054e3c08f20'),
             'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
             'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
         ],
     ],

    /*
     * 微信支付
     *1528321701
     */# todo 修改证书路径
     'payment' => [
         'default' => [
    //         'sandbox'            => env('WECHAT_PAYMENT_SANDBOX', false),
                 'app_id'             => env('WECHAT_PAYMENT_APPID', 'wx8bc33962b0a8ecef'),
                 'mch_id'             => env('WECHAT_PAYMENT_MCH_ID', '1502229801'),
                 'sub_appid'          => env('WECHAT_PAYMENT_SUB_APPID', 'wx68f2dc67c99f4b05'),
                 'sub_mch_id'         => env('WECHAT_PAYMENT_SUB_MCH_ID', '1529568371'),

                 'key'                => env('WECHAT_PAYMENT_KEY', 'KRMpHDp2obFYl41jddEyW3tGOzDx1Ig4'),
                 'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', '../cert/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
                 'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', '../cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
                 'notify_url'         => 'http://tg.hlbapp.com/notify/pay',                           // 默认支付结果通知地址
         ],
         'transfer' =>[
                'app_id'             => env('WECHAT_PAYMENT_APPID', 'wx68f2dc67c99f4b05'),
                'mch_id'             => env('WECHAT_PAYMENT_MCH_ID', '1529568371'),
                 'key'                => env('WECHAT_PAYMENT_KEY', 'KRMpHDp2obFYl41jddEyW3tGOzDx1Ig4'),
                 'cert_path'          => env('WECHAT_PAYMENT_CERT_PATH', '../cert/transfer_cert/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
                 'key_path'           => env('WECHAT_PAYMENT_KEY_PATH', '../cert/transfer_cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
                 'notify_url'         => 'http://tg.hlbapp.com/notify/pay',                           // 默认支付结果通知地址
         ],

     ],

    /*
     * 企业微信
     */
    // 'work' => [
    //     'default' => [
    //         'corp_id' => 'xxxxxxxxxxxxxxxxx',
    ///        'agent_id' => 100020,
    //         'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
    //          //...
    //      ],
    // ],
    # 腾讯位置定位接口
    'lbsqq'     =>  [
      'key' =>  'BAXBZ-UD7E4-WPIUP-DY3VW-T2W62-WVF2E',
    ],
];
