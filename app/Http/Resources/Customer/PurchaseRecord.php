<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class PurchaseRecord extends Resource
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
            'id'            => $this->id,
            'avatar'        => $this->avatar,
            'nickname'      => $this->nickname,
            'num'           => $this->num,
            'createtime'          => $this->createtime,
        ];
    }
}
