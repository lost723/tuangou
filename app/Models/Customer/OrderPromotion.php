<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class OrderPromotion extends BaseModel
{
    const OrderPrefix = '100'; # 子订单号前缀
    const Expire = 0; # 订单超时异常 或 取消
    const Unpaid = 1; # 未支付
    const Refunding = 2; # 退款中
    const Refund = 3; # 已退款
    const UnReceived = 4; # 已支付未发货
    const Dispatched = 5; # 已发货
    const Finished = 6; # 已完成

    protected $fillable = ['customerid', 'orderid', 'lpmid', 'promotionid', 'ordersn', 'num', 'price', 'total',
        'checkcode', 'status', 'note'];
    protected $table = 'order_promotions';

    # 获取全部订单列表 *
    # status 为数组
    static function getOrderPromotions($request)
    {
        $status  = $request->post('status');
        $orderid = $request->post('orderid');
        return DB::table('order_promotions as om')
            ->when($status, function ($query) use ($status) {
                $query->whereIn('om.status', $status);
            })
            ->when($orderid, function ($query) use ($orderid) {
                $query->where('om.orderid', $orderid);
            })
            ->leftjoin('orders', 'orders.id', '=', 'om.orderid')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'om.promotionid')
            ->leftjoin('products as pd', 'pd.id', 'pm.productid')
            ->orderBy('om.status', 'ASC')
            ->orderBy('om.id', 'DESC')
            ->select('om.id', 'om.orderid', 'om.lpmid', 'om.promotionid', 'om.num', 'om.ordersn', 'om.price', 'om.total'
                , 'om.status','orders.createtime',
                'pd.title', 'pd.norm', 'pd.picture', 'pd.quotation')
            ->paginate(self::NPP);
    }

    # 获取子订单详情 *
    static function getOrderPromotionDetail($id)
    {
        return DB::table('order_promotions as om')
            ->where('om.id', $id)
            ->leftjoin('orders', 'orders.id', '=', 'om.orderid')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'om.promotionid')
            ->leftjoin('products as pd', 'pd.id', 'pm.productid')
            ->select('om.id', 'om.orderid', 'om.lpmid', 'om.promotionid', 'om.num', 'om.ordersn', 'om.price',
                'om.total', 'om.status', 'om.checkcode',
                'orders.createtime',
                'pd.title', 'pd.norm', 'pd.picture', 'pd.quotation')
            ->first();
    }


    # 查询订单状态且 该活动未结束 *
    static function checkOrderPromotions($id)
    {
        return DB::table('order_promotions as om')
                ->where('om.id', $id)
//                ->where('om.status', OrderPromotion::Finished)
                ->leftjoin('leader_promotions as lpm', 'lpm.id', '=', 'om.lpmid')
                ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
                ->where('pm.expire', '>', time())
                ->where('pm.status', '=', \App\Models\Business\Promotion::Ordering)
                ->select('*')
                ->first();
    }

    static function findOrderById($id)
    {
        return DB::table('order_promotions')->find($id);

    }

    # 创建 批量订单 *
    static function createOrderPromotions($data)
    {
        return DB::table('order_promotions')->insert($data);
    }


    # 更新用户子订单状态 *
    static function updatePromotionStatus($status, $id)
    {
        return DB::table('order_promotions')
            ->where('id', $id)
            ->update('status', $status);
    }



    # 通过子订单查看 团长及其信息 *
    static function getLeaderInfo($id)
    {
        return DB::table('leaders')
            ->leftjoin('leader_promotions as lm', 'lm.leaderid', '=', 'leaders.id')
            ->leftjoin('order_promotions as om', 'om.lpmid', '=', 'lm.id')
            ->where('om.id', $id)
            ->select('leaders.*')
            ->first();
    }


}
