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
        $list = DB::table(with(new LeaderPromotion)->getTable().' as lm')
            ->whereIn('lm.leaderid', function ($query) use ($commid) {
                $query->select('id')
                    ->from(with(new Leader)->getTable())
                    ->where('community_id',$commid);
            })
            ->where('lm.status', LeaderPromotion::Odering)
            ->leftjoin(with(new BPromotion)->getTable().' as pm', 'lm.promotionid', '=', 'pm.id')
            ->where('pm.expire', '>', time())
            ->leftjoin(with(new Product)->getTable().' as pd', 'pm.productid', '=', 'pd.id')
            ->select('*')
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
}
