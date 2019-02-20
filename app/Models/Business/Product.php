<?php

namespace App\Models\Business;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    const On = 9;  # 正常，在货架
    const Off = 1; # 下架，活动不可见
    const Del = 0; # 删除，商家不可见

    protected $fillable = [
        #todo 填入字段
    ];


    /**
     * 获取某商户的商品列表
     * @param $request
     * @return
     */
    static function getBusinessOwnList($request)
    {
        $orgid = Auth::user()->orgid;
        $status = $request->get('status');
        $filter = $request->get('filter');

        $result = self::select('title', 'picture', 'price', 'norm', 'status')
            ->where('orgid', $orgid)
            ->where('status', '!=', self::Del)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($filter, function ($query) use ($filter){
                $query->where('title', 'like', "%$filter%");
            })
            ->simplePaginate(15);
        return $result;
    }

}
