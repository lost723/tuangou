<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;
use App\Models\Business\Business;
class BusinessPromotions extends Resource
{
    use ImageView;
    /**
     * 团长挑货活动列表页
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   # todo picture 字段处理方式待定
        return [
            'id'        =>  $this->id,                      # 活动id
            'title'     =>  $this->title,                   # 商品名
            'norm'      =>  $this->norm,                    # 商品规格
            'thumb'     =>  $this->ImageViewWithOption(stripslashes($this->thumb), "dissolve"),# 商品图
            'rate'      =>  ($this->rate),                 # 佣金* $this->price
            'price'     =>  sprintf("%.2f", $this->price/100),                   # 活动价
            'quotation' =>  sprintf("%.2f", $this->quotation/100),               # 市场价
            'stockable' =>  $this->stockable,
            'stock'     =>  $this->stock,
            'sales'     =>  $this->sales,
            'expire'    =>  $this->expire,                  # 商品过期时间
            'deliverday'=>  $this->deliveryday,
//            'bussiness' =>  new Business(Business::find($this->orgid)->toArray()),
        ];
    }
}
