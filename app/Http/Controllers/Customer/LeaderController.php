<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Auth\CustomerController;
use App\Models\Customer\Leader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LeaderController extends CustomerController
{
    # 小程序端团长管理
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['register']]);
    }


    # 团长申请
    public function register(Request $request)
    {
        $customer = auth()->user();
        if(empty($customer)) {
            return $this->unauthed();
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
        $idcard_front = $request->post('idcard_front');
        $idcard_back  = $request->post('idcard_back');
        $community_id = $request->post('community_id');
        $customer_id = 1;

        $result = Leader::firstOrCreate(
            [
                'customer_id'   =>  $customer_id,
            ],
            [
                'name'          =>  $request->input('name'),
                'mobile'        =>  $request->input('mobile'),
                'idcard'        =>  $request->input('idcard'),
                'address'       =>  $request->input('address'),
                'community_id'  =>  $community_id >0 ? $community_id : null,
                'idcard_front'  =>  json_encode($idcard_front),
                'idcard_back'   =>  json_encode($idcard_back),
                'status'        =>  Leader::CREATE,
            ]);
        if($result)
        {
            return $this->created('团长申请成功！');
        }

        return $this->warning('团长申请失败，请重新尝试');
    }



}
