<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Business\Product;
use App\Models\Business\Promotion as BPromotion;
use Illuminate\Support\Facades\DB;

class Promotion extends BaseModel
{

    # 获取用户所选小区内所有团长的商品列表
    static function getPromotions($commid)
    {
        # 获取该小区的所有团长下的活动
        $cid = request()->get('cid');
        return DB::table('leader_promotions as lm')
            ->whereIn('lm.leaderid', function ($query) use ($commid) {
                $query->select('id')
                    ->from('leaders')
                    ->where('community_id',$commid);
            })
            ->where('ld.status', Leader::NORMAL)
            ->where('pm.status', BPromotion::Ordering)
            ->where('pm.expire', '>', time())
            ->when($cid, function ($query) use ($cid) {
                $query->where('pd.cid', $cid);
            })
            ->leftjoin('promotions as pm', 'lm.promotionid', '=', 'pm.id')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('categories as cg', 'cg.id', '=', 'pd.cid')
            ->leftjoin('leaders as ld', 'ld.id', '=', 'lm.leaderid')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('lm.*', 'ld.community_id', 'ld.name', 'ld.mobile', 'ld.status as lstatus',
                'pm.orgid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.stock', 'pm.status',
                'pd.title', 'pd.cid', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture',
                'bs.title as btitle' ,
                'cg.title as ctitle', 'cg.parentid', 'cg.level', 'cg.logo')
            ->simplePaginate(BaseModel::NPP);
    }

    # 获取团长下的某商品详情
    static function getPromotion($id)
    {
        return DB::table('leader_promotions as lm')
            ->where('lm.id', $id)
            ->leftjoin('promotions as pm', 'lm.promotionid', '=', 'pm.id')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('categories as cg', 'cg.id', '=', 'pd.cid')
            ->leftjoin('leaders as ld', 'ld.id', '=', 'lm.leaderid')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('lm.*', 'ld.community_id', 'ld.name', 'ld.mobile', 'ld.status as lstatus',
                'pm.orgid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.stock', 'pm.status',
                'pd.title', 'pd.cid', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content',
                'bs.title as btitle' ,
                'cg.title as ctitle', 'cg.parentid', 'cg.level', 'cg.logo')
            ->first();
    }

    # 获取团长某活动 的历史订单
    static function getPurchaseRecord($id)
    {
        return DB::table('order_promotions as om')
            ->where('om.promotionid', $id)
            ->where('om.status', OrderPromotion::Finished)
            ->leftjoin('customers', 'customers.id', '=', 'om.customerid')
            ->select('customers.id', 'customers.nickname', 'om.num', 'om.created_at')
            ->orderBy('om.created_at', 'DESC')
            ->get();
    }

}
