<?php

namespace App\Http\Controllers\Customer;

use App\Http\Resources\Customer\BusinessPromotionDetail;
use App\Http\Resources\Customer\BusinessPromotions;
use App\Models\Auth\Customer;
use App\Models\Business\Promotion;
use App\Models\Common\Leader;
use App\Models\Customer\LeaderPromotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LeaderPromotionController extends Controller
{
    # 小程序端 团长活动管理
    public function __construct()
    {
        # todo 生产环境需取消 非token验证方法
        $this->middleware('auth', ['except' =>  ['getPromotions', 'addPromotions', 'getReceivedPromotions'
        , 'getPromotion']]);
    }

    /**
     * 检测当前用户是否为团长 并且 团长已绑定小区
     * @throws \Exception
     */
    public function checkLeader()
    {
//        $customer =  auth()->user();
        $customer = Customer::find(2);
        if(empty($customer->leader) || ($customer->leader->status != Leader::NORMAL) || empty($customer->leader->commid)   ) {
            throw new \Exception('请先申请成为团长并绑定小区');
        }
    }

    /**
     * 获取团长已参与|可参与的活动列表|获取单个指定活动详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPromotions(Request $request)
    {
        try{
            $this->checkLeader();
            $type = $request->get('type')?: 1;
            # todo 默认团长测试
            # $leader = auth()->user()->leader;
            # 测试 id =1 的leader数据
            $leader = Leader::find(1);
            switch ($type) {
                # 可选活动列表
                case 1:
                    $list = Promotion::getLeaderChoiceList($leader->community_id, $leader->id);
                    break;
                # 已选活动列表
                case 2:
                    $list = LeaderPromotion::getSelectedPromotions($leader->id);
                    break;
                # 获取单个指定活动详情数据
                default:
                    return $this->warning('参数错误');
            }
            return BusinessPromotions::collection($list);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 团长选购查看商品详情
    public function getPromotion($id)
    {
        try{
            $item = LeaderPromotion::getPromotion($id);
            return new BusinessPromotionDetail($item);
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
        # leaderid promotionid num ordersn 生成
        # todo 检查活动库存 挑货校验（活动是否在团长所在小区 提交活动数据是否合法）
        try {
            # $leader = auth()->user()->leader;
            #  todo 默认团长测试
            $leader = Leader::find(2);
            # 整理上传数据
            $data = $request->post('data');
            $promotions = [];
            foreach ($data as $key => $val) {
                $val['ordersn']     = LeaderPromotion::LeaderPrefix.self::createOrderSn();
                $val['leaderid']    = $leader->id;
                $val['status']      = LeaderPromotion::Odering;
                array_push($promotions, $val);
                unset($val);
            }
            DB::beginTransaction();
            $result = LeaderPromotion::addPromotions($promotions);
            DB::commit();
            return $this->ok();
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 获取验收记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReceivedPromotions(Request $request)
    {   #todo 验收代码未完成 团长测试数据
        try{
            # $leader = auth()->user()->leader;
            # 测试 id =1 的leader数据
            $leader = Leader::find(1);
            $id = $request->get('id');
            if(!empty($id)) {
                $list = LeaderPromotion::getReceivedPromotion(1, $id);
            }
            else {
                $list = LeaderPromotion::getReceivedPromotions(1);
            }
            return $this->ok($list);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 生成唯一订单号
     * @return string
     */
    static function createOrderSn()
    {
//        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        return $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

    }
}
