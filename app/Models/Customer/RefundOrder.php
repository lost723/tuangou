<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class RefundOrder extends BaseModel
{
    const RefundPrefix = '200'; # 退款订单号前缀
    const Expire = 0; # 退款失败
    const Refunding = 1; # 退款中 已创建退款订单
    const Finished = 2; # 已退款

    protected $fillable = [];

    # 通过商品订单id查询退款信息
    static function findOrderByOrderid($id)
    {
        return DB::table('refunds')
            ->where('order_promotionid', $id)
            ->first();
    }

    # 通过退款单号 查询退款订单
    static function findOrderByRefundNo($refund_no)
    {
        return DB::table('refunds')
            ->where('refund_no', $refund_no)
            ->first();
    }
    # 通过微信退款单号查询退款信息
    static function findOrderByRefundId($refund_id)
    {
        return DB::table('refunds')
            ->where('refund_id', $refund_id)
            ->first();
    }

    # 创建退款订单
    static function createRefund($data, $id)
    {
        return DB::table('refunds')
            ->updateOrInsert($data,['order_promotionid' => $id]);
    }

    # 更新退款单数据
    static function updateRefund($data, $id)
    {
        return DB::table('refunds')
            ->where('id', $id)
            ->update($data);
    }

    # 更新 退款订单
    static function updateRefundStatus($status, $id)
    {
        return DB::table('refunds')
            ->where('id', $id)
            ->update('status', $status);
    }


}
