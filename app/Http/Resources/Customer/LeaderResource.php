<?php

namespace App\Http\Resources\Customer;

use App\Models\Common\Community;
use Illuminate\Http\Resources\Json\Resource;

class LeaderResource extends Resource
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
            'name'          =>  $this->name,
            'mobile'        =>  $this->mobile,
            'address'       =>  $this->address,
            'commission'    =>  $this->commission,
            'community'     =>  new CommunityResource(Community::find($this->commid)),
        ];
    }
}
