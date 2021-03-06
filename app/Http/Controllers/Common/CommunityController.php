<?php
namespace App\Http\Controllers\Common;

use App\Http\Resources\Customer\LeaderResource;
use App\Models\Auth\Customer;
use App\Models\Common\Leader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            return $this->ok();
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
            # leader 关联小区检测
            $count_leader = Leader::where('commid', $id)->count();
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
            $leader = Leader::find($customer->leaderid);
            if(empty($leader)) {
                return $this->okWithResource([], '该用户还未绑定小区');
            }
            $leader->logo = $leader->customer->avatar;
            $resource = new LeaderResource($leader);
            return $this->okWithResource($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

//    # 通过腾讯地图api搜索附近小区
//    public function searchCommunity()
//    {
//        try {
//            $item = $this->Search('小区');
//            return $this->okWithResource($item);
//        }
//        catch (\Exception $exception) {
//            return $this->warning($exception->getMessage());
//        }
//    }

    /**
     * 用户关联小区
     * @param commid 关联团长id
     * @return \Illuminate\Http\JsonResponse
     */
    public function relateCommunity()
    {
        try {
            $leaderid  = request()->post('leaderid')?:0;
            if(0 < $leaderid) {
                $customer = auth()->user();
                $customer->leaderid = $leaderid;
                $customer->save();
                return $this->okWithResource([], '关联成功');
             }
            throw new \Exception('传入参数异常');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 通过地理位置定位附近的小区
    /**
     * 通过城市id 获取小区列表 => 变更为 获取附近团长社区列表
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
            $result = Community::getCommunityLeaderList($request, $rids);
            $resouce = LeaderResource::collection($result);
            return $this->okWithResourcePaginate($resouce);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }
}
