<?php

namespace App\Http\Resources\Customer;

use App\Models\Customer\OrderPromotion;
use Illuminate\Http\Resources\Json\Resource;

class Order extends Resource
{   # 主订单列表信息
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $request->offsetSet('orderid', $this->id);
        $orderItems = OrderPromotion::getOrderPromotions($request);
        return [
            'id'            =>  $this->id,
            'createtime'    =>  $this->createtime,
            'status'        =>  $this->status,
            'count'         =>  $orderItems->count(),
            'total'         =>  $this->total,
            'trade_no'      =>  $this->trade_no,
            'suborders'     =>  SubOrder::collection($orderItems),
        ];
    }
}
