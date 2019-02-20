<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CommunityResource extends Resource
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
            'type'  =>  'community',
            'id'    =>  (string)$this->id,
            'attribute'     =>  [
                'name'      =>  $this->name,
                'address'   =>  $this->address,
            ],
        ];
    }
}
