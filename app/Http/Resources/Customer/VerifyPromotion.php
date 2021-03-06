<?php

namespace App\Http\Resources\Customer;

use App\Models\Auth\Customer;
use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class VerifyPromotion extends Resource
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
            'title'         =>  $this->title,
            'count'         =>  $this->count,
            'price'         =>  sprintf("%.2f", $this->price/100),
            'quotation'     =>  sprintf("%.2f", $this->quotation/100),
            'rate'          =>  sprintf("%.2f", $this->rate/100),
            'norm'          =>  $this->norm,
            'sales'         =>  $this->sales,
            'stockable'     =>  $this->stockable,
            'stock'         =>  $this->stock,
            'aftersale'   =>  $this->aftersale,
            'intro'         =>  $this->intro,
            'thumb'         =>  $this->ImageViewWithOption($this->thumb, "dissolve"),
        ];
    }
}
