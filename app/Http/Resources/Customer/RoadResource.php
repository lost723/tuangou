<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class RoadResource extends Resource
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
            'id'        =>  $this->id,
            'Letter'    =>  $this->abbr,
            'province'  =>  $this->province,
            'city'      =>  $this->city,
            'district'  =>  $this->district,
            'name'      =>  $this->name,
            'level'     =>  $this->level,
            'life'      =>  $this->life,
        ];
    }
}
