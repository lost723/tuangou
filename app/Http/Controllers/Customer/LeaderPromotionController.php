<?php

namespace App\Http\Controllers\Customer;

use App\Events\LeaderCheckEvent;
use App\Events\LeaderVerifyEvent;
use App\Http\Resources\Customer\BusinessPromotionDetail;
use App\Http\Resources\Customer\BusinessPromotions;
use App\Http\Resources\Customer\CheckPromotinoDetail;
use App\Http\Resources\Customer\CheckPromotion;
use App\Http\Resources\Customer\LeaderPromotionDetial;
use App\Http\Resources\Customer\LeaderPromotions;
use App\Http\Resources\Customer\VerifyPromotion;
use App\Http\Resources\Customer\VerifyPromotionDetail;
use App\Models\Business\Promotion;
use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\OrderPromotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LeaderPromotionController extends Controller
{
    # 小程序端 团长活动管理

    /**
     * 获取团长可选活动列表
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getChoicePromotions(Request $request)
    {
        try{
            $leader = auth()->user()->leader;
            $request->offsetSet('leaderid', $leader->id);
            $list = Promotion::getLeaderChoiceList($leader->commid, $request);
            $result =  BusinessPromotions::collection($list);
            return $this->okWithResourcePaginate($result);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 获取团长已选活动列表
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getOwnPromotions(Request $request)
    {
        try{
            $leader = auth()->user()->leader;
            $list = LeaderPromotion::getSelectedPromotions($leader->id, $request);
            $result =  LeaderPromotions::collection($list);
            return $this->okWithResourcePaginate($result);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 查看商品详情
     * @param Request $request
     * @return BusinessPromotionDetail|\Illuminate\Http\JsonResponse
     */
    public function getPromotionDetail(Request $request)
    {
        try{
            $item = LeaderPromotion::getPromotion($request->post('id'));
            if(empty($item)) {
                throw new \Exception('商品走丢了');
            }
            $resource = new BusinessPromotionDetail($item);
            return $this->okWithResource($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 团长选购查看商品详情
     * @param Request $request
     * @return BusinessPromotionDetail|\Illuminate\Http\JsonResponse
     */
    public function getLeaderPromDetail(Request $request)
    {
        try{
            $item = LeaderPromotion::getLeaderPromotion($request);
            if(empty($item)) {
                throw new \Exception('商品走丢了');
            }
            $resource = new LeaderPromotionDetial($item);
            return $this->okWithResource($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 添加货物至团长活动
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPromotions(Request $request)
    {
        try {
            $leader = auth()->user()->leader;
            $promotionid = $request->post('promotionid');
            $data['promotionid'] = $promotionid;
            $data['leaderid']    = $leader->id;
            $this->checkAddPromotions($data);
            $promotions = [];
            $promotions['promotionid'] = $promotionid;
            $promotions['ordersn']     = LeaderPromotion::LeaderPrefix.self::createOrderSn();
            $promotions['leaderid']    = $leader->id;
            $promotions['active']      = LeaderPromotion::Active;
            $promotions['status']      = LeaderPromotion::UnReceived;
            LeaderPromotion::addPromotions($promotions);
            return $this->okWithResource([], '添加成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # cancelPromotions

    /**
     * 团长取消挑选的货物
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelPromotions(Request $request)
    {
        try{
            $id = $request->post('id');
            DB::table('leader_promotions')
                ->where('id', $id)
                ->update(['active'=>LeaderPromotion::Unactive]);
            return $this->okWithResource([],'取消成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 获取团长待签收列表
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getCheckList(Request $request)
    {
        try{
            $list = LeaderPromotion::getCheckList($request);
            $resouce =  CheckPromotion::collection($list);
            return $this->okWithResourcePaginate($resouce);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 获取团长待签收货物详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getcheckDetail(Request $request)
    {
        try{
            $item = LeaderPromotion::getLeaderPromotion($request);
            if(empty($item)) {
                throw new \Exception('商品走丢了');
            }
            $resource = new CheckPromotinoDetail($item);
            return $this->okWithResource($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 团长签收供应商货物
     * 更新团长订单状态，触发签收事件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doCheck(Request $request)
    {
        # 核销需检测团长订单是否已完成
        try{
            $update['status']     = LeaderPromotion::Received;
            $update['note']       = strval($request->post('note'));
            $update['checktime']  = time(); #  验收时间
            $update['checkcount'] = $request->post('count');
            $id = $request->post('id');
            $lpm = DB::table('leader_promotions')->where('active', LeaderPromotion::Active)->where('id', $id)
                ->select('id', 'status')->first();
            if($lpm && $lpm->status == LeaderPromotion::Received) {
                throw new \Exception('请勿重复签收');
            }
            # 更新订单状态为已签收
            DB::table('leader_promotions')
                ->where('id', $id)
                ->update($update);
             # 触发团长签收事件
            # 实际签收数量
            event(new LeaderCheckEvent($id, $update['checkcount']));
            return $this->okWithResource([], '签收成功');
        }
        catch (\Exception $exception)
        {
            DB::rollback();
            return $this->warning($exception->getMessage());
        }
    }

    #  待核销列表
    public function getVerifyList(Request $request)
    {
        try{
            $leader = auth()->user()->leader;
            $list = LeaderPromotion::getVerifyList($leader->id, $request);

            foreach ($list as &$val) {
                $val->count = DB::table('order_promotions')
                    ->where('lpmid', $val->id)
                    ->where('status', OrderPromotion::Dispatched)
                    ->count();
            }
            $resource = VerifyPromotion::collection($list);
            return $this->okWithResourcePaginate($resource);
        }
        catch (\Exception $exception)
        {
            return $this->warning($exception->getMessage());
        }
    }

    # 核销详情
    public function getVerifyDetail(Request $request)
    {
        try{
            $list = LeaderPromotion::getVerifyDetail($request);
            $resource =   VerifyPromotionDetail::collection($list);
            return $this->okWithResourcePaginate($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 核销
    public function doVerify(Request $request)
    {
         try{
             $request->offsetSet('status', OrderPromotion::Dispatched);
             $order = OrderPromotion::getOrderPromotion($request);
             if(empty($order)) {
                 throw new \Exception('核销码有误!');
             }
             DB::table('order_promotions')->where('id', $order->id)->update(['status' => OrderPromotion::Finished]);
             event(new LeaderVerifyEvent($order->id));
             return $this->okWithResource([], '核销成功');
         }
         catch (\Exception $exception) {
             DB::rollback();
             return $this->warning($exception->getMessage());
         }
    }

    # 团长挑货过滤
    public function checkAddPromotions($data)
    {
        # 检测货物是否在该小区
        # 检测团长是否已选该货物
        $lpm = DB::table('leader_promotions')
            ->where('promotionid', $data['promotionid'])
            ->where('leaderid', $data['leaderid'])
            ->where('active', LeaderPromotion::Active)->first();
        if($lpm) {
            throw new \Exception('请勿重复添加活动');
        }
    }

    /**
     * 生成唯一订单号
     * @return string
     */
    static function createOrderSn()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        return $yCode[intval(date('Y')) - 2019]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%04d', rand(0, 10000));
    }
}
