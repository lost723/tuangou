<?php

namespace App\Http\Controllers\Customer;

use App\Events\CargosEvent;
use App\Events\ShareEvent;
use App\Events\ViewEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    # 浏览活动
    public function viewCount(Request $request)
    {
        try{
            $id = $request->post('id');
            $item = DB::table('leader_promotions')->where('id', $id)->first();
            if(empty($item)) {
                throw new \Exception('请求活动不存在');
            }
            event(new ViewEvent($item->id));
            return $this->okWithResource([], '请求成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 分享活动
    public function shareCount(Request $request)
    {
        try{
            $id = $request->post('id');
            $item = DB::table('leader_promotions')->where('id', $id)->first();
            if(empty($item)) {
                throw new \Exception('请求活动不存在');
            }
            event(new ShareEvent($item->id));
            return $this->okWithResource([], '请求成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 加入购物车活动
    public function cargoCount(Request $request)
    {
        try{
            $id = $request->post('id');
            $item = DB::table('leader_promotions')->where('id', $id)->first();
            if(empty($item)) {
                throw new \Exception('请求活动不存在');
            }
            event(new CargosEvent($item->id));
            return $this->okWithResource([], '请求成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

}
