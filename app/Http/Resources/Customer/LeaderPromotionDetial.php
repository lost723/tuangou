<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class LeaderPromotionDetial extends Resource
{
    use ImageView;
    /**
     *  商品活动详情页
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        # todo picture 字段解析
        return [
            'title'         =>  $this->title,
            'price'         =>  sprintf("%.2f", $this->price/100),
            'quotation'     =>  sprintf("%.2f", $this->quotation/100),
            'rate'          =>  sprintf("%.2f", $this->rate/100),
            'norm'          =>  $this->norm,
            'expire'        =>  $this->expire,
            'deliveryday'   =>  $this->deliveryday,
            'intro'         =>  $this->intro,
            'picture'       =>  $this->when($this->picture, function () {
                $pics = json_decode($this->picture, 1);
                foreach ($pics as &$val) {
                    $val = $this->ImageViewWithOption($val, "dissolve");
                }
                return $pics;
            }),
            'content'       =>  json_decode($this->content, 1),
        ];
    }
}
