<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class OrderPromotion extends Model
{
    const OrderPrefix = '200'; # 支付订单号前缀
    const Expire = 0; # 订单超时异常
    const Unpaid = 1; # 未支付
    const Refund = 2; # 已退款
    const UnReceived = 3; # 已支付待收货
    const Finished = 4; # 已完成
}
