<?php

namespace App\Http\Controllers\Customer;

use EasyWeChat\Factory;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    # 小程序相关系统相关信息 如获取小程序码
    protected $minProgram;
    protected $appcode;
    public function __construct()
    {
        $config = config('wechat.mini_program.default');
        $this->minProgram = Factory::miniProgram($config);
        $this->appcode = $this->minProgram->app_code;
    }

    public function QRcode()
    {
        #
    }
}
