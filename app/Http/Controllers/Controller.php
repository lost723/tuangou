<?php

namespace App\Http\Controllers;

use App\Utils\Reporter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Reporter;

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => []]);
    }

    /**
     * 商家 检查操作权限
     * @param $id
     * @throws BusinessException
     */
    protected function checkBusinessOwnship($id)
    {
        if('distributor' == Auth::guard()){
            if ($id <> Auth::user()->orgid){
                throw new BusinessException('只能操作自己的商户信息');
            }
        }
    }

    /**
     * 运营专用
     * @throws BusinessException
     */
    private function checkTraderOwnship()
    {
        if('trader' <> Auth::guard()){
            throw new BusinessException('运营人员专用！');
        }
    }
}
