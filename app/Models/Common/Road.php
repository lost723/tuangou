<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Road extends BaseModel
{
    //
    protected $fillable = ['parentid', 'level', 'life', 'name', 'path', 'province', 'city', 'district', 'abbr'];

    public function community()
    {
        return $this->hasMany('App\Models\Community','road_id','id');
    }

    # 获取所有街道信息
    static function getRoadList(Request $request)
    {

        $filter = $request->get('filter');
        $id     = $request->get('id');
        $result =  DB::table('roads')
            ->where('level',4)
            ->when($id, function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->when($filter, function ($query) use ($filter) {
                $query->where(function ($qr) use ($filter) {
                    $qr->orWhere('name', 'like',  "%$filter%");
                    $qr->orWhere('province', 'like',  "%$filter%");
                    $qr->orWhere('city', 'like',  "%$filter%");
                    $qr->orWhere('district', 'like',  "%$filter%");
                });
            })
            ->orderBy('abbr', 'ASC')
            ->paginate(self::NPP);

        return self::paginationFormater($result);
    }

    # 获取子类街道信息
    static function getSubItems($id)
    {
        return $result =  DB::table('roads')
                ->where('parentid', $id)
                ->Paginate(BaseModel::NPP);
        return self::paginationFormater($result);
    }

    # 获取所有城市列表
    static function getAllCity()
    {
        return self::where('level', 2)
            ->orderBy('abbr', 'asc')
            ->get()
            ->groupby('abbr')
            ->toArray();
    }


    # 通过城市id 获取街道列表
    static function getRoadsByCityId($id = 0)
    {
        $result = self::whereIn('parentid',function($query) use($id) {
                $query->select('id')
                    ->from(with(new Road)->getTable())
                    ->where('parentid', $id);
            })
            ->where('level', 4)
            ->get(['id']);
        return $result;
    }

    # 通过road_id 获取城市信息
    static function getCityByRoadId($id)
    {
        $item = self::getParentItem($id);
        return self::getParentItem($item['id']);
    }

    # 获取上层城市信息
    static function getParentItem($id)
    {
        if(0 >= $id) {
            return [];
        }
        $item = self::find($id);
        $parent_item = self::find($item['parentid']);
        return $parent_item;
    }

}
