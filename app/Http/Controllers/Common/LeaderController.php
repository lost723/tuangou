<?php

namespace App\Http\Controllers\Common;

use App\Models\Common\Leader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
                'idcard'        =>  'required|string|identitycards',
                'mobile'        =>  'required|string|telphone',
                'name'          =>  'required|string',
                'address'       =>  'required|string',
                'idcard_front'  =>  'required',
                'idcard_back'   =>  'required',
                'formid'        =>  'required'
            ]);
            Redis::set('openid:'.$customer['openid'].':registerformid',$request->post('formid'));
            $all = $request->all();
            $all['idcard_front_url']    = json_encode($all['idcard_front_url']);
            $all['idcard_back_url']     = json_encode($all['idcard_back_url']);
            $all['status']          = Leader::CREATE;
            Leader::create($all);
            return $this->ok();
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }

    }

}
