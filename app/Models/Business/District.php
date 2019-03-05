<?php

namespace App\Models\Business;

use App\Models\BaseModel;
use App\Models\Customer\Community;
use Illuminate\Support\Facades\DB;

class District extends BaseModel
{
    #
    protected $fillable = [
        'orgid', 'title', 'note', 'status'
    ];

    /**
     * 获取某个区域模版附带所有关联小区的id
     * @param $id
     * @return mixed
     */
    static function  findWithItmes($id)
    {
        $obj = self::find($id);
        $items = [];
        $list = DistrictItem::where('distid', $id)->get();
        foreach ($list as $row){
            $items[] = $row['commid'];
        }
        $obj->items = $items;
        return $obj;
    }

    /**
     * 获取小区列表
     * @param $distid
     * @return mixed
     */
    static function getCommunitys($distid)
    {
        $result = DB::table(with(new Community())->getTable())
            ->wherein('id', function ($query) use ($distid) {
                $query->select('commid')
                    ->from(with(new DistrictItem)->getTable())
                    ->where('distid', $distid);
            })
            ->paginate(self::NPP);
        return self::paginationFormater($result);
    }
}
