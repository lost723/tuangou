<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Customer\OrderPromotion;
use Illuminate\Support\Facades\DB;

class Order extends BaseModel
{
    const OrderPrefix = "400"; # 总订单号前缀
    const Cancel = 0; # 订单超时异常
    const Unpaid = 1; # 未支付
    const Finished = 2; # 已支付
    const TimeOut = 15;
//    const LockTime = 2;# 超时未支付 锁定 LockTime后 进行状态更新时 防止超时数据更新不一致

    protected $fillable = ['customerid', 'trade_no', 'transaction_id', 'total', 'createtime', 'paytime', 'status', 'note'];


    # 查询订单
    static function getOrder($request)
    {
        $id             = $request->post('id');
        $trade_no       = $request->post('trade_no');
        $transaction_id = $request->post('ransaction_id');
        $status         = $request->post('status');
        return DB::table('orders')
            ->when($id, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when($trade_no, function ($query) use ($trade_no) {
                $query->where('trade_no', $trade_no);
            })
            ->when($transaction_id, function ($query) use ($transaction_id) {
                $query->where('transaction_id', $transaction_id);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->first();
    }

    # 获取待支付订单列表
    static function getUnpaidList($customerid)
    {
        return DB::table('orders')
            ->where('orders.customerid', $customerid)
            ->where('createtime', '>', (time()-Order::TimeOut*60))
            ->where('orders.status', Order::Unpaid)
            ->select('id', 'createtime', 'status', 'total', 'trade_no')
            ->orderBy('createtime', 'DESC')
            ->paginate(self::NPP);
    }


    # 待支付订单详情信息
    static function getUnpaidOrderDetail($id)
    {
        return DB::table('order_promotions as om')
            ->where('om.orderid', $id)
            ->leftjoin('orders', 'orders.id', '=', 'om.orderid')
            ->leftjoin('leader_promotions as lpm', 'lpm.id', '=', 'om.lpmid')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'om.promotionid')
            ->leftjoin('products as pd', 'pd.id', '=', 'pm.productid')
            ->select('om.id', 'om.price', 'om.num', 'om.total',
                'lpm.leaderid',
                'pd.title' ,'pd.thumb', 'pd.norm')
            ->get();
    }

    # 获取当前订单下的未支付商品的数量
    static function getUnPaidPromotionCount($id)
    {
        return DB::table('order_promotions as om')
            ->where('om.orderid', $id)
            ->selectRaw("Sum(num) as count")
            ->first();
    }

    /**
     * 更新当前消费者用户的超时订单状态
     * @param $customerid
     * # @param
     */
    static function updateTimeoutOrder($customerid)
    {
        Order::where('customerid', $customerid)
            ->where('status', Order::Unpaid)
            ->where('createtime', '>', (time()-Order::TimeOut*60))
            ->update(['status'  =>  Order::Cancel]);
    }

    # 检查订单是否超时 未超时则返回订单信息
    static function checkOrder($id)
    {   #todo 该活动还在进行中
        $order =   DB::table('orders')->where('id', $id)
            ->where('status', Order::Unpaid)
            ->where('createtime','>',(time()- (Order::TimeOut*60)))//->toSql(); dump($order);die;
            ->first();
        return $order;
    }


    # 查找订单
    static function findOrder($id)
    {
        return DB::table('orders')->where('id', $id)
            ->first();
    }


    # 通过总订单查找订单详情列表
    static function getSubPromotions($id)
    {
        return DB::table('order_promotions')
            ->where('orderid', $id)
            ->get();
    }

    # 生成订单
    static function createOrder($data)
    {
        return DB::table('orders')->insertGetId($data);
    }

//    # 更新订单信息
//    static function updateOrder($data, $id)
//    {
//        return DB::table('orders')
//            ->where('id', $id)
//            ->update($data);
//    }

    # 取消订单
    static function cancelCasecadeOrder($id)
    {
        DB::table('orders')->where('id', $id)
            ->update(['status' => Order::Cancel]);
        DB::table('order_promotions')
            ->where('orderid', $id)
            ->update(['status' => OrderPromotion::Expire]);
    }






}
