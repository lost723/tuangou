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
    protected $leader;
    public function __construct()
    {
        # todo 默认第一个用户
//        $this->leader = auth()->user()->leader;
        $this->leader = Leader::find(1);
    }


    /**
     * 获取团长已选活动列表
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getOwnPromotions()
    {
        try{
            $list = LeaderPromotion::getSelectedPromotions($this->leader->id);
            return BusinessPromotions::collection($list);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 获取团长可选活动列表
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getChoicePromotions()
    {
        try{
            $list = Promotion::getLeaderChoiceList($this->leader->commid, $this->leader->id);
            return BusinessPromotions::collection($list);
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
    public function getPromotiondetail(Request $request)
    {
        try{
            $item = LeaderPromotion::getPromotion($request->post('id'));
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
