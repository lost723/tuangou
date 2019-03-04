<?php

namespace App\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\Resource;

class CustomerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'openid'    => $this->openId,
            'mobile'    => $this->mobile,
            'nickname'  => $this->nickName,
            'avatar'    => $this->avatar,
            'country'   => $this->country,
            'province'  => $this->province,
            'city'      => $this->city,
            'gender'    => $this->gender,
            'community' => new CommunityResource($this->community),
            'leader'    => new LeaderResource($this->leader),
        ];
    }
}
