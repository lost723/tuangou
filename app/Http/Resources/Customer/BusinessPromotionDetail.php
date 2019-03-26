<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class BusinessPromotionDetail extends Resource
{
    use ImageView;
    /**
     * 商品活动详情页
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        # todo picture 字段处理方式待定
        return [
            'id'        =>  $this->id,                      # 活动id
            'title'     =>  $this->title,                   # 商品名
            'norm'      =>  $this->norm,                    # 商品规格
            'picture'   =>  $this->when($this->picture, function () {
                $pics = json_decode($this->picture, 1);
                foreach ($pics as &$val) {
                    $val = $this->ImageViewWithOption($val, "dissolve");
                }
                return $pics;
            }),                 # 商品图
            'rate'      =>  sprintf("%.2f", $this->rate/100),                    # 佣金
            'price'     =>  sprintf("%.2f", $this->price/100),                   # 活动价
            'quotation' =>  sprintf("%.2f", $this->quotation/100),               # 市场价
            'stock'     =>  $this->stock,
            'sales'     =>  $this->sales,                   #
            'expire'    =>  $this->expire,                  # 商品过期时间
            'intro'     =>  $this->intro,
            'content'   =>  json_decode($this->content, 1),
//            'bussiness' =>  new Business(\App\Models\Business\Business::find($this->orgid)),
        ];
    }
}
