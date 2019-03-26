<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class UnPaidSubOrderList extends Resource
{
    use ImageView;
    # 未支付子订单列表

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
            'title'         =>  $this->title,
            'thumb'         =>  $this->ImageViewWithOption(stripslashes($this->thumb), "dissolve"),
            'price'         =>  sprintf("%.2f", $this->price/100),
            'num'           =>  $this->num,
            'norm'          =>  $this->norm,
            'total'         =>  sprintf("%.2f", $this->total/100),
        ];
    }
}
