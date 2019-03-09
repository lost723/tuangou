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

    static function getUnpaidList($customerid)
    {
        return DB::table('orders')
            ->where('orders.customerid', $customerid)
//            ->where('createtime', '>', (time()-Order::TimeOut*60))
            ->where('orders.status', Order::Unpaid)
            ->select('*')
            ->orderBy('createtime', 'DESC')
            ->paginate(self::NPP);
    }


    # 待支付订单详情信息
    static function getUnpaidOrderDetail($id)
    {
        return DB::table('order_promotions as om')
            ->where('orders.id', $id)
            ->leftjoin('orders', 'orders.id', '=', 'om.orderid')
            ->leftjoin('leader_promotions as lpm', 'lpm.id', '=', 'om.lpmid')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
            ->leftjoin('products as pd', 'pd.id', '=', 'pm.productid')
            ->select('om.id', 'om.lpmid', 'om.promotionid', 'om.price', 'om.num', 'om.total', 'om.ordersn', 'om.status',
                'lpm.leaderid',
                'pd.title' ,'pd.quotation', 'pd.picture', 'pd.norm')
            ->get();
    }


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


    # 通过总订单查找订单详情列表
    static function getSubPromotions($id)
    {
        return DB::table('order_promotions')
            ->where('order_id', $id)
            ->get();
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






}
