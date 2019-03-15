<?php

namespace App\Http\Resources\Customer;

use App\Models\Auth\Customer;
use Illuminate\Http\Resources\Json\Resource;

class VerifyPromotion extends Resource
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
            'picture'   =>  $this->picture,
            'title' =>  $this->title,
            'norm'  =>  $this->norm,
            'price' =>  $this->price,

        ];
    }
}
