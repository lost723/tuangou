<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Auth\TraderController;
use App\Models\Road;
use Illuminate\Http\Request;

class RoadController extends TraderController
{
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
        # 上级id
        $pid = request()->query('parent_id')?: 0;

        $road = Road::where('parentid',$pid)->first();

        return $this->ok($road);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


    }

    /**
     * 显示当前街道信息
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Road::find($id);
        return $this->ok($item);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Road::find($id);
        return $this->ok($item);
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
        //

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
