<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\WXLocationController;
use App\Http\Resources\Customer\CommunityResource;
use App\Models\Common\Community;
use App\Models\Common\Road;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $result = Community::getCommunityList($request);
            return Community::paginationFormater($result);
        }
        catch (\Exception $exception) {
            $this->warning($exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            #  查看当前小区是否存在
            $name = $request->post('name');
            $road_id = $request->post('road_id');
            # 同一街道不允许同名小区
            $count = Community::where(['name' => $name, 'road_id'=>$road_id])->count();
            if($count >= 1) {
                throw new \Exception('当前小区已存在，请勿重复添加');
            }
            $all = $request->all();
            $all['logo'] = json_encode($all['logo']);
            $item = Community::create($all);
            return $this->ok($item);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $item = Community::find($id);
            return new CommunityResource($item);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $item = Community::find($id);
            return new CommunityResource($item);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $all = $request->all();
            $item = Community::find($id);
            $item->logo = json_encode($all['logo']);
            $item->save($all);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            # customer 关联小区检测
            $count_customer = Customer::where('commid', $id)->count();
            if($count_customer >= 1) {
                throw new \Exception('当前小区被消费者用户关联，请勿删除');
            }
            # leader 关联小区检测
            $count_leader = Customer::where('commid', $id)->count();
            if($count_leader >= 1) {
                throw new \Exception('当前小区被团长关联，请勿删除');
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }



    #小程序端小区相关接口

    # 我的小区信息
    public function myCommunity()
    {
        try {
            $customer = auth()->user();
            if(0 < $customer['commid']) {
                $community = Community::find($customer['commid']);
                if(!empty($community)) {
                    return new CommunityResource($community);
                }
                return $this->ok(['data' => []]);
            }
            throw new \Exception('该用户还未绑定小区');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 通过腾讯地图api搜索附近小区
    public function searchCommunity()
    {
        try {
            $item = WXLocationController::Search('小区');
            if(!empty($item))
            {
                return $this->ok(['data' => $item]);
            }
            return $this->ok(['data' => []]);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }

    }

    /**
     * 用户关联小区
     * @param commid 关联的小区id
     * @return \Illuminate\Http\JsonResponse
     */
    public function relateCommunity()
    {
        try {
            $commid  = request()->post('commid')?:0;
            if(0 < $commid) {
                $customer = auth()->user();
                $customer->commid = $commid;
                $customer->save();
                return $this->ok();
             }
            throw new \Exception('传入参数异常');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 通过地理位置定位附近的小区
    /**
     * 通过城市id 获取小区列表
     * @param $id 城市id
     * @param $filter 小区名称或地址名
     * @param $longitude 经度
     * @param $latitude  纬度
     * @param int page 页码
     * @return bool
     */
    public function CommunityList(Request $request)
    {
        try{
            $cid = $request->get('cid');
            $rids = [];
            if(!empty($cid)) {
                $rids = Road::getRoadsByCityId($cid);
            }
            $result = Community::getCommunityList($request, $rids);
            if(empty($result)) {
                return $this->ok(['data' => []]);
            }
            else {
                return  CommunityResource::collection($result);
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }
}
