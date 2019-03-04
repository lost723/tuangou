<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Community extends BaseModel
{
    protected $fillable = ['name', 'logo', 'road_id', 'address', 'longitude', 'latitude'];

    # 获取小区所属的街道
    public function road()
    {
        return $this->belongsTo('App\Models\Common\Road', 'road_id', 'id');
    }

    # 获取小区列表
    static function getCommunityList(Request $request, $road_ids = [])
    {
        $id     = $request->get('id');
        $filter = $request->get('filter');
        $longitude = request()->get('longitude');
        $latitude  = request()->get('latitude');
        $list = self::when($id, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when($filter, function ($query) use ($filter) {
                $query->orWhere('name', 'like', "%$filter%");
                $query->orWhere('address', 'like', "%$filter%");
            })
            ->when($road_ids, function ($query) use ($road_ids) {
                $query->whereIn('road_id', $road_ids);
            })
            ->when($latitude, function ($query) use ($latitude, $longitude) {
                $query->select(DB::raw("*, st_distance(point(longitude, latitude), 
                point($longitude, $latitude))/0.0111 as distance"))->orderBy('distance', 'asc');
            })
            ->orderBy('id', 'ASC')
            ->paginate(self::NPP);
        return $list;
    }

}
