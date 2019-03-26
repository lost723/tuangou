<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class LeaderPromotions extends Resource
{
    use ImageView;
    /**
     * 用户首页 团长活动列表页
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        # todo picture 字段解析
        return [
            'id'            =>  $this->id,
//            'leaderid'      =>  $this->leaderid,
            'promotionid'   =>  $this->promotionid,
            'title'         =>  $this->title,
            'price'         =>  sprintf("%.2f", $this->price/100),
            'quotation'     =>  sprintf("%.2f", $this->quotation/100),
            'rate'          =>  sprintf("%.2f", $this->rate/100),
            'norm'          =>  $this->norm,
            'intro'         =>  $this->intro,
            'sales'         =>  $this->sales,
            'stockable'     =>  $this->stockable,
            'stock'         =>  $this->stock,
            'status'        =>  $this->status,
            'expire'        =>  $this->expire,
            'deliveryday'   =>  $this->deliveryday,
            'thumb'         =>  $this->ImageViewWithOption(stripslashes($this->thumb), "dissolve"),
//            'ordersn'       =>  $this->ordersn,
        ];
    }
}
