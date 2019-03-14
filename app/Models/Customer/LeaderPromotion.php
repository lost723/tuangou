<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Business\Promotion as BPromotion;
use Illuminate\Support\Facades\DB;

class LeaderPromotion extends BaseModel
{
    const LeaderPrefix = '300'; # 团长订单号前缀
    const Terminated = 0;   # 异常结束 已取消
//    const Odering = 1;      # 进行中
    const Dispatching = 1;  # 待送中
    const Received = 2;     # 已签收

    protected $table = 'leader_promotions';
    protected $fillable = ['leaderid', 'promotionid', 'num', 'sales', 'ordersn', 'check', 'expire', 'status'];

    /**
     * 获取活动详情
     * 2019/2/28 晚测试
     * @return mixed
     */
    static function getPromotion($id)
    {
        $result = DB::table('promotions as pm')
            ->where('pm.id', $id)
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content')
            ->first();
        return $result;
    }

    # 获取团长商品详情
    static function getLeaderPromotion($request)
    {
        # todo 测试数据 屏蔽进行中的订单
        $id = $request->get('id');
        return DB::table('leader_promotions as lpm')
            ->where('lpm.id', $id)
            ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
            ->leftjoin('products as pd', 'pd.id', '=', 'pm.productid')
            ->select('lpm.*',
                'pm.orgid', 'pm.optid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.deliveryday', 'pm.aftersale',
                'pm.status',
                'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content')
            ->first();
    }

    /**
     * 获取团长的 活动列表
     * 2019/2/28 晚测试
     * @param $leaderid
     * @return mixed
     */
    static function getSelectedPromotions($leaderid, $request)
    {
        $filter = $request->get('filter');
        $result = DB::table('leader_promotions as lpm')
                ->where('pm.expire', '>', time())
                ->where('lpm.leaderid', $leaderid)
                ->where('lpm.status', LeaderPromotion::Dispatching)
                ->when($filter, function ($query) use ($filter) {
                    $query->where(function ($qr) use ($filter) {
                       $qr->orWhere('pd.title', 'like', "%$filter%");
                       $qr->orWhere('bs.title', 'like', "%$filter%");
                    });
                })
                ->where('pm.status', BPromotion::Ordering)
                ->where('pm.expire', '>', time())
                ->where('pm.stock', '>', 0)
                ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
                ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
                ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
                ->select('lpm.*',
                    'pm.orgid', 'pm.optid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.deliveryday', 'pm.aftersale',
                    'pm.status',
                    'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture')
                ->Paginate(self::NPP);
        return $result;
    }

    /**
     * 添加选货至团长活动列表
     * 2019/2/28 晚测试
     * @param $data
     * @return mixed
     */
    static function addPromotions($data) {
        return  DB::table(with(new LeaderPromotion)->getTable())
            ->insert($data);
    }


    # 获取验收列表|验收记录
    static function getCheckList($request)
    {
        $filter = $request->get('filter');
        $status = $request->get('status');
        return DB::table('leader_promotions as lpm')
            ->where('lpm.status', $status)
            ->when($filter, function ($query) use ($filter) {
                $query->where(function ($qr) use ($filter) {
                    $qr->orWhere('pd.title', 'like', "%$filter%");
                    $qr->orWhere('bs.title', 'like', "%$filter%");
                });
            })
            ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('lpm.*',
                'pm.orgid', 'pm.optid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.deliveryday', 'pm.aftersale',
                'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture')
            ->paginate(self::NPP);
    }


    # 获取待核销订单列表
    static function getVerifyList($leaderid, $request)
    {
        $filter = $request->get('filter');
        return DB::table('leader_promotions as lm')
            ->where('lm.status', LeaderPromotion::Received)
            ->where('lm.leaderid', $leaderid)
            ->where('om.status', OrderPromotion::Dispatched)
            ->when($filter, function ($query) use ($filter) {
                $query->where(function ($qr) use ($filter) {
                    $qr->orWhere('pd.title', 'like', "%$filter%");
                });
            })
            ->leftjoin('order_promotions as om', 'om.lpmid', 'lm.id')
            ->join('promotions as pm', 'pm.id', '=', 'lm.promotionid')
            ->join('products as pd', 'pd.id', '=', 'pm.productid')
            ->select('lm.*',
                'pm.price',
                'pd.title', 'pd.picture', 'pd.norm')
            ->paginate(self::NPP);
    }

    # 待核销订单详情
    static function getVerifyDetail($request)
    {
        # 团长待核销活动id
        $id = $request->post('id');
        return DB::table('order_promotions as om')
            ->where('om.lpmid', $id)
            ->where('om.status', OrderPromotion::Dispatched)
            ->leftjoin('customers as cms', 'cms.id', '=', 'om.customerid')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'om.promotionid')
            ->leftjoin('products as pd', 'pd.id', '=', 'pm.productid')
            ->select('cms.avatar', 'cms.nickname', 'cms.mobile','om.num')
            ->paginate(self::NPP);
    }



    /**
     * 更新团长活动的状态
     * @param $id       活动id
     * @param $status   订单状态
     * @return mixed
     */
    static function updatePromotionStatus($id, $status)
    {
        return self::find($id)->update(['status' => $status]);
    }

    # 更新团长活动销量
    static function incPromotionSales($id, $num = 1)
    {
        return  DB::table('order_promotions')
            ->where('id', $id)
            ->increment('sales', $num);
    }

    # 更新团长活动销量
    static function decPromotionSales($id, $num = 1)
    {
        return  DB::table('order_promotions')
            ->where('id', $id)
            ->decrement('sales', $num);
    }

    # 通过团长订单id 来更新 商户订单的销量
    static function incBusinessPromotionSales($id, $num = 1)
    {
        return DB::table('promotions as pm')
            ->join('leader_promotions as lpm', 'lpm.promotionid', '=', 'pm.id')
            ->where('lpm.id', $id)
            ->increment('pm.sales', $num);

    }
    # 通过团长订单id 来更新 商户订单的销量
    static function decBusinessPromotionSales($id, $num = 1)
    {
        return DB::table('promotions as pm')
            ->join('leader_promotions as lpm', 'lpm.promotionid', '=', 'pm.id')
            ->where('lpm.id', $id)
            ->decrement('pm.sales', $num);

    }


}

