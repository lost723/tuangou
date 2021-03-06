<?php

namespace App\Http\Controllers\Common;

use App\Models\Common\Leader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class LeaderController extends Controller
{
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    # 团长申请
    public function register(Request $request)
    {
        try {
            $customer = auth()->user();
            if(empty($customer)) {
                throw new \Exception('未授权');
            }
            if(!empty($leader = $customer->leader)) {
                return $this->note('请勿重复提交团长表单！');
            }
            $request->validate([
                'formid'        =>  'required|string'
            ]);

            Redis::set('openid:'.$customer['openid'].':registerformid',$request->post('formid'));
            $all = $request->all();
            $all['customerid']      = $customer->id;
            $all['status']          = Leader::CREATE;
            Leader::createLeader($all);
            return $this->okWithResource([], '提交成功');
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }

    }

}
