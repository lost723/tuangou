<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\RoadException;
use App\Models\Common\Community;
use App\Models\Common\Road;
use App\Http\Controllers\Weixin\WXLocationController;
use App\Http\Resources\Customer\RoadResource;

class RoadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $result =  Road::getRoadList($request);
            return $this->ok($result);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            # 返回父级菜单
            $pid = request()->get('parent_id')?: 0;
            $road = Road::where('id',$pid)->first();
            return $this->ok($road);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
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
            //validate input
            $all = $request->all();
            $item = Road::create($all);
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
            $item = Road::find($id);
            return $this->ok($item);
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
            $item = Road::find($id);
            return $this->ok($item);
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
            $all  = $request->all();
            $item = Road::find($id);

            $name = $request->post('name');
            $count = Road::where(['name' => $name, 'parentid' => $item->parentid])->count();
            if($count >= 1) {
                throw new RoadException($name.'已经存在了！');
            }
            $item->update($all);
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
            $count = Road::where('parentid',$id)->count();
            if($count > 0) {
                throw new RoadException('请先删除其下级菜单！');
            }
            # 需先解除和小区关联的项
            $count = Community::where('road_id', $id)->count();
            if($count >= 1) {
                throw new RoadException('还有小区在关联本街道，请确认后再删除！');
            }
            self::destroy($id);
            return $this->ok();
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }

    }

    # 坐标获取城市信息
    # 使用坐标通过腾讯地图获取城市信息
    public function myCity()
    {
        try {
            # 获取当前城市
            $location = WXLocationController::getLocation();
            if(empty($location)) {
                throw new \Exception('暂时无法获取当前位置信息');
            }
            $item = Road::where('name', $location['city'])->first();
            if(empty($item)) {
                return $this->ok(['data' => []]);
            }
            return new RoadResource($item);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 获取城市列表
    public function listCity()
    {
        try{
            $city = Road::getAllCity();
            if(empty($city)) {
                throw new \Exception('城市跑丢了!');
            }
            $list = [];
            foreach ($city as $key=>$val) {
                $tmp['letter'] = $key;
                $tmp['list'] = [];
                foreach ($val as $k=>$v) {
                    array_push($tmp['list'], ['id' => $v['id'], 'name' => $v['name']]);
                }
                array_push($list, $tmp);
                unset($tmp);
            }
            unset($city);
            return $this->ok($list);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }

    }

    public function getSubRoads(Request $request)
    {
        try {
            $parentid = $request->get('pid') > 0 ? $request->get('pid') : 0;
            return Road::getSubItems($parentid);
        } catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }



}
