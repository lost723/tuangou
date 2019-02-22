<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Support\Facades\DB;

class LeaderPromotion extends BaseModel
{
    const Terminated = -1;  # 异常结束
    const Received = 0;     # 已签收
    const Odering = 1;      # 进行中
    const Dispatching = 2;  # 配送中

    protected $fillable = ['leaderid', 'promotionid', 'num', 'sales', 'ordersn', 'check', 'expire', 'status'];


    # 获取团长的 订单列表
    static function getSelectedPromotions($leaderid)
    {
        $result = DB::table(with(new LeaderPromotion)->getTable())
                ->where('expire', '>', time())
                ->where('leaderid', $leaderid)
                ->where('status', LeaderPromotion::Odering)
                ->leftjoin('promotions as pm', 'pm.id', '=', 'promotionid')
                ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
                ->select('pm.*', 'pd.title', 'pd.norm', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content')
                ->simplePaginate(self::NPP);
        return $result;
    }

    /**
     * 添加选货至团长订单
     * @param Request $request
     * # @param
     */
    public function addToOrderList(Request $request) {

    }
}
