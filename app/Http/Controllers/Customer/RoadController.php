<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Auth\CustomerController;
use App\Http\Controllers\Common\WXLocationController;
use App\Http\Resources\RoadResource;
use App\Models\Customer\Road;
use Illuminate\Http\Request;

class RoadController extends CustomerController
{
    # 小程序端街道管理
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['listCity', 'myCity', 'getSubRoads']]);
    }


}
