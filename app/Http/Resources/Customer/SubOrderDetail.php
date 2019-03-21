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
        $leader = Leader::find($this->leaderid);
        return [
            'id'           => $this->oid,
            'createtime'   => $this->createtime,
            'total'        => sprintf("%.2f", $this->total/100),
            'checkcode'    => $this->checkcode,
            'trade_no'     => $this->ordersn,
            'count'        => $this->num,
            'state'        => $this->when($this->status == OrderPromotion::CHANGE, function () {
                return OrderPromotion::REFUNDCLOSE;
            }, function() {
                return $this->status;
            }),
            'suborder'     => [[
                'id'            =>  $this->id,
                'title'         =>  $this->title,
                'thumb'         =>  $this->thumb,
                'price'         =>  sprintf("%.2f", $this->price/100),
                'num'           =>  $this->num,
                'norm'          =>  $this->norm,
                'total'         => sprintf("%.2f", $this->total/100),
            ]],
            'pickupStation'    =>  new PickUpStation($leader),
        ];
    }
}
