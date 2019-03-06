<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class SubOrder extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
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
            'ordersn'       =>  $this->ordersn,
            'createtime'    =>  $this->createtime,
        ];
    }
}
