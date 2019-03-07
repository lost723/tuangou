<?php

namespace App\Http\Resources\Customer;

use App\Models\Common\Leader;
use App\Models\Customer\OrderPromotion;
use Illuminate\Http\Resources\Json\Resource;

class SubOrderDetail extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $leader = OrderPromotion::getLeaderInfo($this->id);
        return [
            'id'            =>  $this->id, # 子订单id
            'orderid'       =>  $this->orderid,
            'lpmid'         =>  $this->lpmid,
            'promotionid'   =>  $this->promotionid,
            'title'         =>  $this->title,
            'picture'       =>  $this->picture,
            'price'         =>  $this->price,
            'quotation'     =>  $this->quotation,
            'num'           =>  $this->num,
            'norm'          =>  $this->norm,
            'total'         =>  $this->total,
            'status'        =>  $this->status,
            'checkcode'     =>  $this->checkcode,
            'ordersn'       =>  $this->ordersn,
            'createtime'    =>  $this->createtime,
            'leader'        =>  new LeaderResource($leader),
        ];
    }
}
