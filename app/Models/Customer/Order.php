<?php

namespace App\Customer\Models;

use App\Models\BaseModel;
use App\Models\Customer\OrderPromotion;
use Illuminate\Support\Facades\DB;

class Order extends BaseModel
{
    protected $fillable = ['customerid', 'trade_no', 'transaction_id', 'total', 'paytime', 'status', 'note'];
    const OrderPrefix = "400"; # 总订单号前缀
    const Cancel = 0; # 订单超时异常
    const Unpaid = 1; # 未支付
    const Finished = 2; # 已支付

    const TimeOut = 15;



    # 检查订单是否超时
    static function checkOrder($id)
    {   #todo 该活动还在进行中
        return DB::table('orders')->where('id', $id)
            ->where('status', Order::Unpaid)
            ->where('paytime','>',(time()-Order::TimeOut*60))
            ->first();
    }

    # 查找订单
    static function findOrder($id)
    {
        return DB::table('orders')->where('id', $id)
            ->first();
    }
    # 通过订单号查询订单
    static function findOrderByTradeNo(string $trade_no)
    {
        return DB::table('orders')
            ->where('trade_no', $trade_no)
            ->first();
    }



    # 生成订单
    static function createOrder($data)
    {
        return DB::table('orders')->insertGetId($data);
    }

    # 更新订单信息
    static function updateOrder($data, $id)
    {
        return DB::table('orders')
            ->where('id', $id)
            ->update($data);
    }
    # 取消订单
    static function cancelCasecadeOrder($id)
    {

        DB::table('orders')->where('id', $id)
            ->update(['status' => Order::Cancel]);
        DB::table('order_promotions')
            ->where('orderid', $id)
            ->update(['status' => OrderPromotion::Expire]);

    }

    # 通过总订单查找订单详情列表
    static function getSubPromotions($id)
    {
        return DB::table('order_promotions')
            ->where('order_id', $id)
            ->get();
    }


    # 待支付订单详情信息
    static function getOrderDetail($id)
    {
        return DB::table('order_promotions as om')
                ->where('orders.id', $id)
                ->where('orders.status', Order::Unpaid)
                ->leftjoin('orders', 'orders.id', '=', 'om.orderid')
                ->leftjoin('leader_promotions as lm', 'lm.id', '=', 'om.promotionid')
                ->leftjoin('promotions as pm', 'pm.id', '=', 'lm.promotionid')
                ->leftjoin('products as pd', 'pd.id', '=', 'pm.productid')
                ->select('orders.id', 'orders.paytime', 'orders.status', 'orders.total as ttotal', 'orders.trade_no',
                    'om.id as oid', 'om.num', 'om.total',
                    'lm.leaderid', 'pm.price',
                    'pd.title' ,'pd.quotation', 'pd.picture', 'pd.norm')
                ->get()
                ->groupBy('leaderid');
    }

    # 商品订单详情
    static function getOrderPromotionDetail($id)
    {
        return DB::table('order_promotions as om')
            ->where('om.id', $id)
            ->leftjoin('leader_promotions as lm', 'lm.id', '=', 'om.promotionid')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'lm.promotionid')
            ->leftjoin('products as pd', 'pd.id', '=', 'pm.productid')
            ->select('om.id', 'om.price', 'om.num', 'om.total', 'om.checkCode', 'om.status',
                'pm.expire', 'pm.deliveryday', 'pm.aftersale', '')
            ->first();
    }



}
