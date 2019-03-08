<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class OrderItem extends Resource
{   # 子订单列表信息
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
            'lpmid'         =>  $this->lpmid,
            'title'         =>  $this->title,
            'picture'       =>  $this->picture,
            'price'         =>  $this->price,
            'quotation'     =>  $this->quotation,
            'num'           =>  $this->num,
            'norm'          =>  $this->norm,
            'total'         =>  $this->total,
            'ordersn'       =>  $this->ordersn,
            'status'         =>  $this->status,
        ];
    }
}
