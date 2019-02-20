<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Promotion extends Model
{
    const Deleted = 0;      #删除的活动
    const Unpublished = 1;  #未发布的活动
    const Ordering = 2;     #进行中的活动
    const Storing = 3;      #备货中
    const Dispatching = 4;  #配送中
    const Received = 5;     #签收完成
    const Terminated = 8;   #活动异常结束，下架
    const Finished = 9;     #过了售后期，结束

    protected $fillable = [];


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
    
    /**
     * 获取还没有结束的活动
     * @param $id
     * @return mixed
     */
    static function getUnfinished($productId)
    {
        return self::where('productid', $productId)
            ->where('status', '!=', self::Finished)
            ->get();
    }

    /**
     * 获取活动详情，融合商品信息
     * @param $id
     * @return mixed
     */
    static function getDetails($id)
    {
        $item = DB::table('promotions as pm')
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.norm', 'pd.intro', 'pd.picture', 'pd.content')
            ->leftJoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->where('pm.id', $id)
            ->get();

        return $item;
    }
}
