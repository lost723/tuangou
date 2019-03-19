<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Business\Promotion as BPromotion;
use Illuminate\Support\Facades\DB;

class LeaderPromotion extends BaseModel
{
    const LeaderPrefix = '300'; # 团长订单号前缀
    const Unactive = 0;   # 取消挑选该活动
    const Active = 1;     # 挑选该活动

    const UnReceived = 0; # 未签收
    const Received = 1;   # 已签收
    const Finished = 2;   # 已完成


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
                'pm.orgid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.deliveryday', 'pm.aftersale',
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
                ->where('lpm.leaderid', $leaderid)
                ->where('lpm.active', LeaderPromotion::Active)
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
                    'pm.orgid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.deliveryday', 'pm.aftersale',
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
        return  DB::table('leader_promotions')
            ->insert($data);
    }

    /**
     * 检索 已激活的 未签收 且 活动在配送中的团长活动
     * 获取验收列表|验收记录
     * @param $request
     * @return mixed
     */
    static function getCheckList($request)
    {
        $filter = $request->get('filter');
        return DB::table('leader_promotions as lpm')
            ->where('lpm.active', LeaderPromotion::Active)
            ->where('lpm.status', LeaderPromotion::UnReceived)
            ->where('pm.status', BPromotion::Dispatching)
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
            ->orderBy('lpm.status')
            ->paginate(self::NPP);
    }


    /**
     * 获取团长已签收 未完成的活动列表
     * 获取待核销订单列表
     * @param $leaderid
     * @param $request
     * @return mixed
     */
    static function getVerifyList($leaderid, $request)
    {
        $filter = $request->get('filter');
        return DB::table('leader_promotions as lm')
            ->where('lm.status', LeaderPromotion::Received)
            ->where('lm.leaderid', $leaderid)
            ->when($filter, function ($query) use ($filter) {
                $query->where(function ($qr) use ($filter) {
                    $qr->orWhere('pd.title', 'like', "%$filter%");
                });
            })
            ->join('promotions as pm', 'pm.id', '=', 'lm.promotionid')
            ->join('products as pd', 'pd.id', '=', 'pm.productid')
            ->select('lm.*',
                'pm.price',
                'pd.title', 'pd.picture', 'pd.norm')
            ->paginate(self::NPP);
    }

    /**
     *
     * 待核销订单详情
     * todo 同一活动合并订单
     * @param $request
     * @return mixed
     */
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
//            ->select(DB::raw("distinct cms.id, cms.avatar, cms.nickname, cms.mobile, om.num"))
            ->paginate(self::NPP);
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

