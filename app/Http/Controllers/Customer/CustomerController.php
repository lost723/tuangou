<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\Resource;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' =>  ['index']]);
    }


    # 消费者用户 常用数据展示
    public function index(Request $request)
    {
        try{
            $commid = $request->get('commid');
            $list = Promotion::getPromotions($commid);
//            return Resource::collection($list);
            return $this->ok($list->toArray());
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 团长下的某商品详情页 团长活动->活动>商品
    public function show($id)
    {
        try{
            $item = Promotion::getPromotion($id);
            return new Resource($item);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 商品的购买记录
    public function history($id)
    {

    }


}
