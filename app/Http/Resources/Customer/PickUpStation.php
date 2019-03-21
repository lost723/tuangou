<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class PickUpStation extends Resource
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
            'leaderno'      =>  $this->leaderno,
            'logo'          =>  $this->logo,
            'commtitle'     =>  $this->commtitle,
            'alias'         =>  $this->alias,
            'name'          =>  $this->name,
            'mobile'        =>  $this->when(isset($this->mobile), function () {
                return $this->mobile;
            }),
            'address'       =>  $this->address,
        ];
    }
}
