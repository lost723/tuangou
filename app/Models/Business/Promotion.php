<?php

namespace App\Models\Business;

use App\Models\BaseModel;
use App\Models\LeaderOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer\LeaderPromotion;


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

    protected $fillable = [
        'orgid', 'optid', 'productid', 'price', 'quotation', 'expire', 'deliveryday',
        'stock', 'stockable', 'aftersale', 'status'
    ];


    /**
     * 获取某商户的活动列表
     * @param $request
     * @return
     */
    static function getBusinessOwnList($request)
    {
        $orgid = Auth::user()->orgid;
        $pid = $request->get('pid');
        $date = $request->get('date');
        $status = $request->get('status');
        $filter = $request->get('filter');

        $result = DB::table('promotions as pm')
            ->where('pm.orgid', $orgid)
            ->where('pm.status', '!=', self::Deleted)
            ->when($pid, function ($query) use ($pid) {
                $query->where('pm.productid', $pid);
            })
            ->when($date, function ($query) use ($date) {
                $query->where('pm.expire','>=', (strtotime($date[0])))
                    ->where('pm.expire','<', (strtotime($date[1])));
            })
            ->when($status, function ($query) use ($status) {
                $query->where('pm.status', $status);
            })
            ->when($filter, function ($query) use ($filter){
                $query->where('pd.title', 'like', "%$filter%");
            })
            ->leftJoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.thumb')
            ->paginate(self::NPP);

        return self::paginationFormater($result);
    }


    /**
     * 获取团长的挑货列表
     * @param $commid   小区id
     * @param $leaderid 团长id
     * @return mixed
     */
    static function getLeaderChoiceList($commid, $request)
    {
        # todo 把团长已经挑选的，剔除掉
        $leaderid = $request->get('leaderid');
        $filter   = $request->get('filter');
        $resutl = DB::table('promotions as pm')
            ->where('expire', '>', time())
            ->where('pm.status',  self::Ordering)

            ->wherein('distid', function ($query) use ($commid) {
                $query->select('distid')
                    ->from(with(new DistrictItem)->getTable())
                    ->where('commid', $commid);
            })
            ->when($filter, function ($query) use ($filter) {
                $query->where(function ($qr) use ($filter) {
                    $qr->orwhere('pd.title', 'like', "%$filter%");
                    $qr->orwhere('bs.title', 'like', "%$filter%");
                });
            })
            ->whereNotExists( function ($query) use ($leaderid) {
                $query->select('lpm.promotionid')
                    ->from('leader_promotions as lpm')
                    ->where('lpm.leaderid', $leaderid)
                    ->where('lpm.active', LeaderPromotion::Active)
                    ->whereRaw('lpm.promotionid = pm.id');
            })
            ->join('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content')
            ->Paginate(self::NPP);
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
