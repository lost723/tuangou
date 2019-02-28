<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class BusinessPromotionDetail extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        # todo picture 字段处理方式待定
        # todo 内容 详情 处理待定
        return [
            'id'        =>  $this->id,                      # 活动id
            'title'     =>  $this->title,                   # 商品名
            'norm'      =>  $this->norm,                    # 商品规格
            'pic'       =>  $this->picture,                 # 商品图
            'rate'      =>  ($this->rate * $this->price),   # 佣金
            'price'     =>  $this->price,                   # 活动价
            'quotation' =>  $this->quotation,               # 市场价
            'stock'     =>  $this->stock,
            'sales'     =>  $this->sales,                   #
            'expire'    =>  $this->expire,                  # 商品过期时间
            'intro'     =>  $this->intro,
            'content'   =>  $this->content,
            'bussiness' =>  new Business(\App\Models\Business\Business::find($this->orgid)),
        ];
    }
}
