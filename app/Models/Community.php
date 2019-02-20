<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Community extends Model
{

    # 获取小区所属的街道
    public function road()
    {
        return $this->belongsTo('App\Models\Road', 'road_id', 'id');
    }
    
    /**
     * 通过坐标获取附近小区
     * @param
     */
    public function getListByCoordinate($longitude, $latitude, $page = 1, $limit=10)
    {
        $offset = ($page-1)*$limit;
        if(0 > $offset) {
            $offset = 0;
        }
        $lists = DB::select('select c.id, c.name, c.longitude, c.latitude, r.province, 
                    r.city, r.district, r.name as rname, c.address,
                    (st_distance(point(longitude, latitude), point(?, ?))/?) 
                      as distance from communities  as c left join roads as r on c.road_id = r.id
                        order by distance asc LIMIT ?,?', [$longitude,$latitude,0.0111,$offset,$limit]);
        return $lists;
    }
}
