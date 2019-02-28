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
        $list = DB::table('leader_promotions as lm')
            ->whereIn('lm.leaderid', function ($query) use ($commid) {
                $query->select('id')
                    ->from(with(new Leader)->getTable())
                    ->where('community_id',$commid);
            })
            ->where('pm.status', BPromotion::Ordering)
            ->leftjoin(with(new BPromotion)->getTable().' as pm', 'lm.promotionid', '=', 'pm.id')
            ->where('pm.expire', '>', time())
            ->leftjoin(with(new Product)->getTable().' as pd', 'pm.productid', '=', 'pd.id')
            ->when($cid, function ($query) use ($cid) {
                $query->where('pd.cid', $cid);
            })
            ->select('lm.id as lid','pm.*', 'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture')
            ->simplePaginate(BaseModel::NPP);
        return $list;
    }

    # 获取团长下的某商品详情
    static function getPromotion($id)
    {
        $item = DB::table(with(new LeaderPromotion)->getTable().' as lm')
            ->whereIn('lm.id', $id)
            ->where('lm.status', LeaderPromotion::Odering)
            ->leftjoin(with(new BPromotion)->getTable().' as pm', 'lm.promotionid', '=', 'pm.id')
            ->where('pm.expire', '>', time())
            ->leftjoin(with(new Product)->getTable().' as pd', 'pm.productid', '=', 'pd.id')
            ->select('*')
            ->first();
        return $item;
    }

    # 获取团长某活动 的历史订单
    static function getPromotionHistory($id)
    {
        $list = null;

        return $list;
    }


    # 获取团长某活动的价格信息
    static function getPromotionPrice($id)
    {
        $item = DB::table(with(new LeaderPromotion)->getTable().' as lm')
            ->where('lm.id', $id)
            ->leftjoin(with(new BPromotion)->getTable().' as pm', 'lm.promotionid', '=', 'pm.id')
            ->select('lm.id', 'lm.promotionid', 'lm.num', 'lm.sales', 'pm.price', 'pm.status', 'pm.stock')
            ->first();
        return $item;
    }
}
