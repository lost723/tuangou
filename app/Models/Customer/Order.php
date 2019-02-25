<?php

namespace App\Customer\Models;

use App\Models\BaseModel;
use App\Models\Customer\OrderPromotion;

class Order extends BaseModel
{
    protected $fillable = ['customerid', 'trade_no', 'transaction_id', 'total', 'paytime', 'status', 'note'];
    const OrderPrefix = "400"; # 子订单号前缀
    const Cancel = 0; # 订单超时异常
    const Unpaid = 1; # 未支付
    const Finished = 2; # 已支付

    const TimeOut = 15;

    static function createOrder($data)
    {
        return DB::table('orders')->insertGetId($data);
    }

    # 检查订单是否超时
    static function checkOrder($id)
    {
        return DB::table('orders')->where('id', $id)
            ->where('paytime','>',(time()-Order::TimeOut*60))
            ->first();
    }

    static function updateCasecadeOrder($id)
    {

        DB::table('orders')->where('id', $id)
            ->update(['status' => Order::Cancel]);
        DB::table('order_promotions')
            ->where('orderid', $id)
            ->update(['status' => OrderPromotion::Expire]);

    }
}
