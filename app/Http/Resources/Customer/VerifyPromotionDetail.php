<?php

namespace App\Http\Resources\Customer;

use App\Utils\ImageView;
use Illuminate\Http\Resources\Json\Resource;

class VerifyPromotionDetail extends Resource
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
            'avatar'    =>  $this->avatar,
            'nickname'  =>  $this->nickname,
            'mobile'    =>  $this->mobile,
            'num'       =>  $this->num,
            'status'    =>  $this->status,
            'checktime' =>  $this->checktime,
        ];
    }
}
