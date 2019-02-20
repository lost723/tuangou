<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
    //

    # 通过城市id 获取街道列表

    public function getRoadsByParentId($id = 0)
    {
        if(0 >= $id) {
            return false;
        }
        $result = $this->where('path','like',"%$id%")
                ->where('leveltype', 4)
                ->get(['id'])->implode('id',',');
        if(!empty($result)) {
            $result = explode(',',$result);
        }
        return $result;
    }

}
