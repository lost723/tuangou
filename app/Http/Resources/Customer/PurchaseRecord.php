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
            'num'           => $this->num,
            'created_at'    => $this->created_at,
        ];
    }
}
