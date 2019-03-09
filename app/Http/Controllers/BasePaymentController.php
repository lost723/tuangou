<?php

namespace App\Http\Controllers;

use EasyWeChat\Factory;

class BasePaymentController extends Controller
{
    public $payment;
    public $config;
    public function __construct()
    {
        $this->config = config('wechat.payment.default');
        $this->payment = Factory::payment($this->config);
    }
}
