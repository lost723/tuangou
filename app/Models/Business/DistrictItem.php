<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DistrictItem extends Model
{

    protected $fillable = ['distid', 'commid'];

    /**
     * 批量添加
     * @param $distid
     * @param $commids
     * @return mixed
     */
    public static function addAll($distid, $commids)
    {
        $obj = new self();
        $datas = [];
        foreach ($commids as $commid){
            $datas[] = ['distid'=>$distid, 'commid'=>$commid];
        }
        $rs = DB::table($obj->getTable())->insert($datas);
        return $rs;
    }

}
