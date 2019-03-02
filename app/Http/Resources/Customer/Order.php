<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class Order extends Resource
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
            'id'    =>  $this->id,
            'paytime'    =>  $this->id,
            'status'    =>  $this->id,
            'norm'    =>  $this->id,
            'picture'    =>  $this->id,
            'quotation'    =>  $this->id,
            'total'    =>  $this->id,
            'title'    =>  $this->id,
            'price'    =>  $this->id,

        ];
    }
}
