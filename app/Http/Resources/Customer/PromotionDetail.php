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
                'deliveryday'=> $this->deliveryday,
                'sales'     =>  $this->sales,
                'stockable' =>  $this->stockable,
                "stock"     =>  $this->when(($this->stockable <> 0), function () {
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
                'rate'      =>  sprintf("%.2f", $this->rate/100),
                'quotation' =>  sprintf("%.2f", $this->quotation/100),
                'intro'     =>  $this->intro,
                'thumb'     =>  stripslashes($this->thumb),
                'picture'   =>  json_decode($this->picture),
                'content'   =>  json_decode($this->content),
            ],
        ];
    }
}
