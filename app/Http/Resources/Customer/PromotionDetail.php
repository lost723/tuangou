<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class PromotionDetail extends Resource
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
            "id"    =>  $this->id,
            "promotion" =>[
                'id'        =>  $this->promotionid,
                'price'     =>  sprintf("%.2f",$this->price / 100),
                'expire'    =>  $this->expire,
                'stock'     =>  $this->stock,
                'stockable' =>  $this->stockable,
                "sales"     =>  $this->when(($this->stockable <> 0), function () {
                    return $this->stock;
                }, function(){
                    return 1000000000;
                }),
                'status'    =>  $this->status,
            ],
            "bussiness" =>  $this->btitle,
            "product"   =>  [
                'title'     =>  $this->title,
                'norm'      =>  $this->norm,
                'rate'      =>  number_format($this->rate/100,2),
                'quotation' =>  $this->quotation,
                'intro'     =>  $this->intro,
                'picture'   =>  json_decode($this->picture),
                'content'   =>  json_decode($this->content),
            ],
        ];
    }
}
