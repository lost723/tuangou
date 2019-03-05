<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Auth\TraderController;
use App\Http\Resources\CommunityResource;
use App\Models\Auth\Customer;
use App\Models\BaseModel;
use App\Models\Customer\Community;
use Illuminate\Http\Request;

class CommunityController extends TraderController
{
    # 后台小区管理
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'register']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        try {
            $rid = $request->get('rid');
            $list = Community::where('road_id', $rid)->OrderBy('id','asc')->paginate(Community::NPP);
            return Community::paginationFormater($list);
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
            # customer 关联小区
            $count_customer = Customer::where('community_id', $id)->count();
            if($count_customer >= 1) {
                throw new \Exception('当前小区被消费者用户关联，请勿删除');
            }
            # leader 关联小区
            $count_leader = Customer::where('community_id', $id)->count();
            if($count_leader >= 1) {
                throw new \Exception('当前小区被团长关联，请勿删除');
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }
}
