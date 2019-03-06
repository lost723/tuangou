<?php

namespace App\Models\Business;

use App\Models\BaseModel;
use App\Models\LeaderOrder;
use function foo\func;
use http\Env\Request;
use Illuminate\Support\Facades\DB;

class Promotion extends BaseModel
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
     * 获取某商户的活动列表
     * @param $request
     * @return
     */
    static function getBusinessOwnList($request)
    {
        $orgid = Auth::user()->orgid;
        $pid = $request->get('pid');
        $status = $request->get('status');
        $filter = $request->get('filter');

        $result = DB::table('promotions as pm')
            ->where('pm.orgid', $orgid)
            ->where('pm.status', '!=', self::Del)
            ->when($pid, function ($query) use ($pid) {
                $query->where('pm.productid', $pid);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('pm.status', $status);
            })
            ->when($filter, function ($query) use ($filter){
                $query->where('pd.title', 'like', "%$filter%");
            })
            ->leftJoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.intro') ->simplePaginate(self::NPP);
        return $result;
    }


    /**
     * 获取团长的挑货列表
     * @param $commid   小区id
     * @param $leaderid 团长id
     * @return mixed
     */
    static function getLeaderChoiceList($commid, $leaderid)
    {
        # todo 把团长已经挑选的，剔除掉
        $resutl = DB::table('promotions as pm')
            ->where('expire', '>', time())
            ->where('pm.status',  self::Ordering)

            ->wherein('distid', function ($query) use ($commid) {
                $query->select('distid')
                    ->from(with(new DistrictItem)->getTable())
                    ->where('commid', $commid);
            })
            ->whereNotExists( function ($query) use ($leaderid) {
                $query->select('lpm.promotionid')
                    ->from('leader_promotions as lpm')
                    ->where('lpm.leaderid', $leaderid)
                    ->whereRaw('lpm.promotionid = pm.id');
            })
            ->join('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content')
            ->simplePaginate(self::NPP);
        return $resutl;
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
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.intro', 'pd.picture', 'pd.content')
            ->leftJoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->where('pm.id', $id)
            ->get();

        return $item;
    }
}
