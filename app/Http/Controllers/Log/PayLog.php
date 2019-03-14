<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/14
 * Time: 18:38
 */

namespace App\Http\Controllers\Log;


class PayLog
{
    private $config;
    public function __construct()
    {
        $this->config = config('app.');
    }
}