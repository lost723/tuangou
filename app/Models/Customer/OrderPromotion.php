<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderPromotion extends Model
{
    protected $fillable = ['customerid', 'orderid', 'promotionid', 'ordersn', 'num', 'price', 'total', 'status', 'note'];
    const OrderPrefix = '100'; # 子订单号前缀
    const Expire = 0; # 订单超时异常 或 取消
    const Unpaid = 1; # 未支付
    const Refunding = 2; # 退款中
    const Refund = 3; # 已退款
    const UnReceived = 4; # 已支付未发货
    const Dispatched = 5; # 已发货
    const Finished = 6; # 已完成

    # 创建 批量订单
    static function createOrderPromotions($data)
    {
        return DB::table('order_promotions')->insert($data);
    }

    # 查询订单状态且 该活动未结束
    static function checkOrderPromotions($id)
    {
        return DB::table('order_promotions as om')
                ->where('om.id', $id)
//                ->where('om.status', OrderPromotion::Finished)
                ->leftjoin('leader_promotions as lpm', 'lpm.id', '=', 'om.promotionid')
                ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
                ->where('pm.expire', '>', time())
                ->where('pm.status', '=', \App\Models\Business\Promotion::Ordering)
                ->select('*')
                ->get();
    }



}
