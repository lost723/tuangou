<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class Promotions extends Resource
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
            "sales" =>  $this->sales,
            "promotion" =>[
                'id'        =>  $this->promotionid,
                'price'     =>  $this->price,
                'expire'    =>  $this->expire,
                'stockable' =>  $this->stockable,
                'stock'     =>  $this->when(($this->stockable <> 0), function () {
                    return 1000000000;
                }, function(){
                    return $this->stock;
                }),
            ],
            "bussiness" =>  $this->btitle,
            "product"   =>  [
                'title'     =>  $this->title,
                'norm'      =>  $this->norm,
                'rate'      =>  $this->rate,
                'quotation' =>  $this->quotation,
                'intro'     =>  $this->intro,
                'thumb'     =>  stripslashes($this->thumb),
                'picture'   =>  $this->when(!empty($this->picture),function () {
                    return json_decode($this->picture);
                }),
            ],
        ];
    }
}
