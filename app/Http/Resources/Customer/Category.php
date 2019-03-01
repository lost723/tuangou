<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class Category extends Resource
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
            'id'            =>  $this->id,
            'parentid'      =>  $this->parentid,
            'level'         =>  $this->level,
            'title'         =>  $this->title,
            'logo'          =>  $this->logo,
        ];
    }
}
