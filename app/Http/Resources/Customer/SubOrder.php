<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class SubOrder extends Resource
{
    use ImageView;
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
            'price'         =>  sprintf("%.2f",$this->price / 100),
            'num'           =>  $this->num,
            'norm'          =>  $this->norm,
            'total'         =>  sprintf("%.2f",$this->total / 100),
            'status'        =>  $this->status,
            'ordersn'       =>  $this->ordersn,
            'checkcode'     =>  $this->when($this->checkcode, function (){
               return $this->checkcode;
            }),
            'qrcode'        =>  $this->when($this->checkcode, function (){
                return $this->createQrCode($this->checkcode);
            }),
            'createtime'    =>  $this->createtime,
        ];
    }
}
