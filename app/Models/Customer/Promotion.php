<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Business\Product;
use App\Models\Common\Leader;
use App\Models\Business\Promotion as BPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Promotion extends BaseModel
{

    #
    /**
     * 只展示激活状态的活动 并且 活动处于 进行中
     * 获取用户所选小区内所有团长的商品列表
     * @param Request $request
     * @return mixed
     */
    static function getPromotions(Request $request)
    {
        # 获取该小区的所有团长下的活动
        $catid = $request->post('cid'); # 商品分类id
        $leaderid = $request->post('leaderid');
        $filter = $request->post('filter');
        return DB::table('leader_promotions as lm')
            ->where('lm.leaderid', $leaderid)
            ->where('ld.status', Leader::NORMAL)
            ->where('lm.active', LeaderPromotion::Active)
            ->where('pm.status', BPromotion::Ordering)
            ->where('pm.expire', '>', time())
            ->when($filter, function ($query) use ($filter) {
                $query->where('pd.title', 'like', "%$filter%");
            })
            ->when($catid, function ($query) use ($catid) {
                $query->where('pd.catid', $catid);
            })
            ->leftjoin('leaders as ld', 'ld.id', '=', 'lm.leaderid')
            ->leftjoin('promotions as pm', 'lm.promotionid', '=', 'pm.id')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('lm.*',
                'pm.productid', 'pm.price', 'pm.expire', 'pm.quotation','pm.stockable', 'pm.stock', 'pm.status',
                'pm.deliveryday',
                'pd.title', 'pd.catid', 'pd.norm', 'pd.rate',  'pd.intro', 'pd.thumb', 'pd.picture',
                'bs.title as btitle')
            ->Paginate(BaseModel::NPP);
    }

    # 获取团长下的某商品详情
    static function getPromotion($id)
    {
        return DB::table('leader_promotions as lm')
            ->where('lm.id', $id)
            ->where('lm.active', LeaderPromotion::Active)
            ->where('pm.status', BPromotion::Ordering)
            ->leftjoin('promotions as pm', 'lm.promotionid', '=', 'pm.id')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('lm.*',
                'pm.productid', 'pm.price', 'pm.expire', 'pm.deliveryday', 'pm.stockable', 'pm.stock',  'pm.status',
                'pd.title', 'pd.catid', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.thumb', 'pd.picture', 'pd.content',
                'bs.title as btitle' )
            ->first();
    }

    #

    /**
     * 获取团长某活动 的历史订单
     * todo 测试使用 注释 订单完成状态
     * @param $request
     * @return mixed
     */
    static function getPurchaseRecord($request)
    {
        $id     = $request->post('id');
        $skip   = $request->post('skip');
        $result = DB::table('order_promotions as om')
            ->where('om.lpmid', $id)
//            ->where('om.status', OrderPromotion::Finished)
//            ->whereIn('pm.id',function ($query) {
////                $query->select('')
//            })
            ->when($skip, function ($query) use ($skip) {
                $query->skip($skip);
            })
            ->leftjoin('customers', 'customers.id', '=', 'om.customerid')
            ->select('customers.id', 'customers.avatar', 'customers.nickname', 'om.num', 'om.created_at')
            ->orderBy('om.created_at', 'DESC')
            ->Paginate(BaseModel::NPP);
        return $result;
    }

    static function getRecommendPromotions($request, $catid)
    {
        $id = $request->post('id');
        return DB::table('leader_promotions as  lm')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'lm.promotionid')
            ->leftjoin('products as pd', 'pd.id', '=', 'pm.productid')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->where('pd.catid', $catid)
            ->whereNotExists(function ($query) use ($id) {
                $query->select('id')
                    ->from('leader_promotions as lpm')
                    ->whereRaw("lpm.id = lm.id")
                    ->where('lm.id', $id);
            })
            ->select('lm.*',
                'pm.orgid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.deliveryday', 'pm.stockable', 'pm.stock',  'pm.status',
                'pd.title', 'pd.catid', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.thumb', 'pd.picture', 'pd.content',
                'bs.title as btitle' )
            ->paginate(3);
    }

    # 根据团长活动id 获取分类信息
    static function getCategroy($id)
    {
        return DB::table('leader_promotions as lm')
            ->leftjoin('promotions as pm', 'pm.id', '=', 'lm.promotionid')
            ->leftjoin('products as pd', 'pd.id', 'pm.productid')
            ->leftjoin('categories as cats', 'cats.id', '=', 'pd.catid')
            ->where('lm.id', $id)
            ->select('cats.id')
            ->first();
    }

}
