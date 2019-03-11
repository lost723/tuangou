<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class CheckPromotion extends Resource
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
            'id'            =>  $this->id,
            'leaderid'      =>  $this->leaderid,
            'promotionid'   =>  $this->promotionid,
            'productid'     =>  $this->productid,
            'orgid'         =>  $this->orgid,
            'optid'         =>  $this->optid,
            'title'         =>  $this->title,
            'price'         =>  $this->price,
            'rate'          =>  $this->rate,
            'norm'          =>  $this->norm,
            'num'           =>  $this->num,
            'sales'         =>  $this->sales,
            'status'        =>  $this->status,
            'checktime'     =>  $this->checktime,
            'quotation'     =>  $this->quotation,
            'expire'        =>  $this->expire,
            'deliveryday'   =>  $this->deliveryday,
            'aftersale'     =>  $this->aftersale,
            'intro'         =>  $this->intro,
            'picture'       =>  $this->picture,
            'ordersn'       =>  $this->ordersn,
        ];
    }
}
