<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Weixin\WXPayConfigController;
use App\Http\Controllers\Customer\WXPayNotifyController;
use App\Http\Controllers\Customer\WXRefundNotifyController;

class NotifyController
{

    # 接收异步通知
    # 支付通知处理
    public function payResult()
    {
        $notify = new WXPayNotifyController();
        $config = new WXPayConfigController();
        $notify->Handle($config, false);
    }

    #  退款通知处理
    public function refundResult()
    {
        $notify = new WXRefundNotifyController();
        $config = new WXPayConfigController();
        $notify->Handle($config, false);
    }
}
