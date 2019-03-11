<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class VerifyPromotionDetail extends Resource
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
            'avatar'    =>  $this->avatar,
            'nickname'  =>  $this->nickname,
            'mobile'    =>  $this->mobile,
            'num'       =>  $this->num,
        ];
    }
}
