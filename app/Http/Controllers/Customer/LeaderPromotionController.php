<?php

namespace App\Http\Controllers\Customer;

use App\Models\Business\Promotion;
use App\Models\Leader;
use App\Models\LeaderPromotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderPromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' =>  ['getPromotions']]);
    }

    # 获取团长已参与|可参与的活动列表
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
                $list = LeaderPromotion::getSelectedPromotions(1);
                break;
            default:
                return $this->warning('参数错误');
        }
        return $this->ok($list);
    }
}
