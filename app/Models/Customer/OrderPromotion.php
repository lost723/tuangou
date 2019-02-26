<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class OrderPromotion extends Model
{
    protected $fillable = ['customerid', 'orderid', 'promotionid', 'ordersn', 'num', 'price', 'total', 'status', 'note'];
    const OrderPrefix = '100'; # 子订单号前缀
    const Expire = 0; # 订单超时异常 或 取消
    const Unpaid = 1; # 未支付
    const Refund = 2; # 已退款
    const UnReceived = 3; # 已支付待收货
    const Finished = 4; # 已完成

    static function createOrderPromotions($data)
    {
        return DB::table('order_promotions')->insert($data);
    }
}
