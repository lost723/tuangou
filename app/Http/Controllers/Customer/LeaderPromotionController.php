<?php

namespace App\Http\Controllers\Customer;

use App\Models\Business\Promotion;
use App\Models\Customer\Leader;
use App\Models\Customer\LeaderPromotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderPromotionController extends Controller
{
    # 小程序端 团长活动管理
    public function __construct()
    {
        $this->middleware('auth', ['except' =>  ['getPromotions', 'addPromotions', 'getReceivedPromotions'
        , 'createOrderSn']]);
    }


    /**
     * 获取团长已参与|可参与的活动列表|获取单个指定活动详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPromotions(Request $request)
    {
        $type = $request->get('type')?: 1;
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
            case 3:
                $id = $request->get('id');
                $list = LeaderPromotion::getPromotion($leader->id, $id);
                break;
            default:
                return $this->warning('参数错误');
        }
        return $this->ok($list);
    }



    /**
     * 添加数据至团长活动列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPromotions(Request $request)
    {
        # leaderid
        # promotionid
        # num
        # ordersn 生成
        # 检查活动库存
        try {
            # $leader = auth()->user()->leader;
            # 测试 id =1 的leader数据
            $leader = Leader::find(1);
            # 整理上传数据
            $data = $request->post('data');

            $data = [];
            foreach ($data as $key => $val) {
                $val['ordersn']     = $this->createOrderSn();
                $val['leaderid']    = $leader->id;
                array_push($data, $val);
                unset($val);
            }

            DB::beginTransaction();
            $result = LeaderPromotion::addPromotions($data);
            DB::commit();
            return $this->ok($result);
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
    {
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
        return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}
