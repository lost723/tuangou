<?php

namespace App\Http\Controllers\Common;

use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoticeController extends Controller
{
    public $app;
    public function __construct()
    {
        parent::__construct();
        $config = config('wechat.mini_program.default');
        $this->app = Factory::miniProgram($config);
    }

    public function sendTemplateMessage($openid, $templateid, $page='pages/home/home/home', $formid, $data)
    {
        $reuslt = $this->app->template_message->send([
            'touser' => $openid,
            'template_id' => $templateid,
            'page' => $page,
            'form_id' => $formid,
            'data' => $data,
        ]);
        return $reuslt;
    }
}
