<?php

namespace App\Http\Resources;

use App\Models\Road;
use function foo\func;
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
            'id'        =>  $this->id,
            'name'      =>  $this->name,
            'address'   =>  $this->address,
            'longitude' =>  $this->longitude,
            'latitude'  =>  $this->latitude,
            'distance'  =>  $this->when(!empty($this->distance),function() {
                return number_format($this->distance,1);
            }),
            'road'      =>  new RoadResource($this->road),
            'city'      =>  new RoadResource(Road::getCityByRoadId($this->road->id)),
        ];
    }
}
