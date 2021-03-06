<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class Business extends Resource
{
    /**
     *  商户信息
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title'     =>  $this->title,
            'phone'     =>  $this->manager,
            'address'   =>  $this->address,
            'manager'   =>  $this->manager
        ];
    }
}
