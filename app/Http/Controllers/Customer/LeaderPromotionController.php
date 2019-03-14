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
    protected $leader;
    public function __construct()
    {   # todo 问题
        $customer = auth()->user();
        if($customer) {
            $this->leader = $customer->leader;
        }
    }

    /**
     * 获取团长可选活动列表
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getChoicePromotions(Request $request)
    {
        try{
            $request->offsetSet('leaderid', $this->leader->id);
            $list = Promotion::getLeaderChoiceList($this->leader->commid, $request);
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
            $list = LeaderPromotion::getSelectedPromotions($this->leader->id, $request);
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
     * 添加数据至团长活动列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPromotions(Request $request)
    {
        # todo 检查活动库存 挑货校验（活动是否在团长所在小区 提交活动数据是否合法）
        try {
            # 整理上传数据
            $data = $request->post('data');
            $this->checkAddPromotions($data);
            $promotions = [];
            foreach ($data as $key => $val) {
                $val['ordersn']     = LeaderPromotion::LeaderPrefix.self::createOrderSn();
                $val['leaderid']    = $this->leader->id;
                $val['status']      = LeaderPromotion::Odering;
                array_push($promotions, $val);
                unset($val);
            }
            DB::beginTransaction();
            LeaderPromotion::addPromotions($promotions);
            DB::commit();
            return $this->okWithResource([], '添加成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # cancelPromotions
    public function cancelPromotions(Request $request)
    {
        try{
            $id = $request->post('id');
            $count = DB::table('order_promotions')
                ->where('lpmid', $id)
                ->count();
            if($count >= 1) {
                throw new \Exception('已有用户购买该活动，禁止取消');
            }
            DB::table('leader_promotions')
                ->where('id', $id)
                ->update(['status'=>LeaderPromotion::Terminated]);
            return $this->okWithResource([],'取消成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 待验收列表
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

    # 待验收详情
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

    public function doCheck(Request $request)
    {
        # todo  货物签收 为order_promotions 生成 checkcode 更新用户订单为 待提货
        try{
            $update['status']     = LeaderPromotion::Received;
            $update['note']       = strval($request->post('note'));
            $update['checktime']  = time(); #  验收时间
            $id = $request->post('id');
            DB::beginTransaction(); # 更新订单状态为已签收
            DB::table('leader_promotions')
                ->where('id', $id)
                ->update($update);
            DB::commit();
            event(new LeaderCheckEvent($id));
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
            $list = LeaderPromotion::getVerifyList($this->leader->id, $request);
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
            if(empty($list)) {
                throw new \Exception('请检查该订单是否已核销!');
            }
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
             DB::beginTransaction();
             $order->save(['status' => OrderPromotion::Finished]);
             DB::commit();
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
