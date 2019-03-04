<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\Business\Promotion as BPromotion;
use Illuminate\Support\Facades\DB;

class LeaderPromotion extends BaseModel
{
    const LeaderPrefix = '300'; # 团长订单号前缀
    const Terminated = 0;   # 异常结束
    const Odering = 1;      # 进行中
    const Dispatching = 2;  # 配送中
    const Received = 3;     # 已签收

    protected $fillable = ['leaderid', 'promotionid', 'num', 'sales', 'ordersn', 'check', 'expire', 'status'];

    /**
     * 获取活动详情
     * 2019/2/28 晚测试
     * @return mixed
     */
    static function getPromotion($id)
    {   # todo 该货物是否属于团长 状态标识
        $result = DB::table('promotions as pm')
            ->where('pm.id', $id)
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->select('pm.*', 'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture', 'pd.content')
            ->first();
        return $result;
    }

    /**
     * 获取团长的 活动列表
     * 2019/2/28 晚测试
     * @param $leaderid
     * @return mixed
     */
    static function getSelectedPromotions($leaderid)
    {
        $result = DB::table('leader_promotions as lpm')
                ->where('pm.expire', '>', time())
                ->where('lpm.leaderid', $leaderid)
                ->where('pm.status', BPromotion::Ordering)
                ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
                ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
                ->select('pm.*', 'pd.title', 'pd.norm', 'pd.rate', 'pd.quotation', 'pd.intro', 'pd.picture')
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

    /**
     * 获取验收记录
     * @param $lid
     * @return mixed
     */
    static function getReceivedPromotions($lid)
    {
        # todo 获取佣金数据 需集合用户订单
       $result = DB::table(with(new LeaderPromotion)->getTable().' as lpm')
           ->where('lpm.status',LeaderPromotion::Received)
           ->where('lpm.leaderid', $lid)
           ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
           ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
           ->select('lpm.*', 'pm.*', 'pd.title','pd.norm', 'pd.quotation', 'pd.intro', 'pd.picture')
           ->Paginate(self::NPP);
       return $result;
    }

    /**
     * 获取指定id 单个验收活动记录
     * @param $lid  团长id
     * @param $id   活动id
     * @return mixed
     */
    static function getReceivedPromotion($lid, $id)
    {
        # todo 获取佣金数据 需集合用户订单
        $result = DB::table(with(new LeaderPromotion)->getTable().' as lpm')
            ->where('lpm.status',LeaderPromotion::Received)
            ->where('lpm.leaderid', $lid)
            ->where('lpm.id', $id)
            ->leftjoin('promotions as pm', 'pm.id', '=', 'lpm.promotionid')
            ->leftjoin('products as pd', 'pm.productid', '=', 'pd.id')
            ->select('lpm.*', 'pm.*', 'pd.title','pd.norm', 'pd.quotation', 'pd.intro', 'pd.picture')
            ->first();
        return $result;
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
