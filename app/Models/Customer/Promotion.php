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

    # 获取用户所选小区内所有团长的商品列表
    static function getPromotions(Request $request)
    {
        # 获取该小区的所有团长下的活动
        $catid = $request->post('cid'); # 商品分类id
        $commid = $request->post('commid');
        return DB::table('leader_promotions as lm')
            ->whereIn('lm.leaderid', function ($query) use ($commid) {
                $query->select('id')
                    ->from('leaders')
                    ->where('commid',$commid);
            })
            ->where('ld.status', Leader::NORMAL)
            ->where('lm.status', LeaderPromotion::Odering)
            ->where('pm.status', BPromotion::Ordering)
            ->where('pm.expire', '>', time())
            ->when($catid, function ($query) use ($catid) {
                $query->where('pd.catid', $catid);
            })
            ->leftjoin('promotions as pm', 'lm.promotionid', '=', 'pm.id')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('categories as cg', 'cg.id', '=', 'pd.catid')
            ->leftjoin('leaders as ld', 'ld.id', '=', 'lm.leaderid')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('lm.*', 'ld.commid', 'ld.name', 'ld.mobile', 'ld.status as lstatus',
                'pm.orgid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.stock', 'pm.status',
                'pd.title', 'pd.catid', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture',
                'bs.title as btitle' ,
                'cg.title as ctitle', 'cg.parentid', 'cg.level', 'cg.logo')
            ->Paginate(BaseModel::NPP);
    }

    # 获取团长下的某商品详情
    static function getPromotion($id)
    {
        return DB::table('leader_promotions as lm')
            ->where('lm.id', $id)
//            ->where('lm.status', LeaderPromotion::Odering)
            ->leftjoin('promotions as pm', 'lm.promotionid', '=', 'pm.id')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->leftjoin('categories as cg', 'cg.id', '=', 'pd.catid')
            ->leftjoin('leaders as ld', 'ld.id', '=', 'lm.leaderid')
            ->leftjoin('businesses as bs', 'bs.id', '=', 'pm.orgid')
            ->select('lm.*', 'ld.commid', 'ld.name', 'ld.mobile', 'ld.status as lstatus',
                'pm.orgid', 'pm.productid', 'pm.price', 'pm.expire', 'pm.stock', 'pm.status',
                'pd.title', 'pd.catid', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content',
                'bs.title as btitle' ,
                'cg.title as ctitle', 'cg.parentid', 'cg.level', 'cg.logo')
            ->first();
    }

    # 获取团长某活动 的历史订单
    static function getPurchaseRecord($request)
    {
        $id =   $request->post('id');
        $skip = $request->post('skip');
        $result=DB::table('order_promotions as om')
            ->where('om.lpmid', $id)
            ->where('om.status', OrderPromotion::Finished)
            ->when($skip, function ($query) use ($skip) {
                $query->skip($skip);
            })
            ->leftjoin('customers', 'customers.id', '=', 'om.customerid')
            ->select('customers.id', 'customer.avatar', 'customers.nickname', 'om.num', 'om.created_at')
            ->orderBy('om.created_at', 'DESC')
            ->Paginate(BaseModel::NPP);
        return self::paginationFormater($result);
    }

}
