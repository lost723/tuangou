<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class LeaderPromotionDetial extends Resource
{
    /**
     *  商品活动详情页
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        # todo picture 字段解析
        return [
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
            'status'        =>  $this->status,
            'quotation'     =>  $this->quotation,
            'expire'        =>  $this->expire,
            'deliveryday'   =>  $this->deliveryday,
            'aftersale'     =>  $this->aftersale,
            'intro'         =>  $this->intro,
            'picture'       =>  $this->picture,
            'ordersn'       =>  $this->ordersn,
            'content'       =>  $this->content,
        ];
    }
}
