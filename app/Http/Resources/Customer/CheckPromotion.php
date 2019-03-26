<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class CheckPromotion extends Resource
{
    use ImageView;
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
            'promotionid'   =>  $this->promotionid,
            'title'         =>  $this->title,
            'price'         =>  sprintf("%.2f", $this->price/100),
            'quotation'     =>  sprintf("%.2f", $this->quotation/100),
            'rate'          =>  sprintf("%.2f", $this->rate/100),
            'norm'          =>  $this->norm,
            'sales'         =>  $this->sales,
            'stockable'     =>  $this->stockable,
            'stock'         =>  $this->stock,
            'expire'        =>  $this->expire,
            'deliveryday'   =>  $this->deliveryday,
            'intro'         =>  $this->intro,
            'thumb'         =>  $this->ImageViewWithOption($this->thumb, "dissolve"),
        ];
    }
}
