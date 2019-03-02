<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class OrderItem extends Resource
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
            'title'         =>  $this->title,
            'picture'       =>  $this->picture,
            'price'         =>  $this->price,
            'quotation'     =>  $this->quotation,
            'norm'          =>  $this->norm,
            'total'         =>  $this->total,

        ];
    }
}
