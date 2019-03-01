<?php

namespace App\Models\Business;

use App\Models\BaseModel;

class District extends BaseModel
{
    #
    protected $fillable = [
        'orgid', 'title', 'note', 'status'
    ];

    /**
     * 获取某个区域模版附带所有小区详情
     * @param $id
     * @return mixed
     */
    static function  findWithItmes($id)
    {
        $obj = self::find($id);
        # todo 需要关联小区表
        $obj->items = DistrictItem::where('distid', $id)->get();
        return $obj;
    }
}
