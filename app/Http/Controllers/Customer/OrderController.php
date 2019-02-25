<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    # 消费者用户 订单相关接口处理

    # 计算商品价格
    public function calculate()
    {

    }

    #  生成订单
    public function createOrder(Request $request)
    {
        #todo 更新库存数量
        try{

        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    #  支付订单
    public function payOrder($id)
    {

    }


    # 取消订单
    public function cancelOrder($id)
    {

    }

    # 订单列表
    public function listOrder()
    {

    }

    # 订单详情
    public function detailOrder($id)
    {

    }

    # 订单退款
    public function refundOrder($id)
    {

    }






}
