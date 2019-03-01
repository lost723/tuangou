<?php

namespace App\Http\Controllers\Trader;

use App\Exceptions\RoadException;
use App\Http\Controllers\Auth\TraderController;
use App\Models\Community;
use App\Models\Road;
use Illuminate\Http\Request;

class RoadController extends TraderController
{
    # 后台街道管理
    public function __construct()
    {
        # 测试 过滤token验证
        $this->middleware('auth', ['except' => ['index', 'create']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

    }

    /**
     * 显示上级街道信息
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
     * 显示当前街道信息
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
}
