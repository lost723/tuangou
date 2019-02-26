<?php

namespace App\Http\Controllers\Customer;

use App\Customer\Models\Order;
use App\Http\Controllers\Common\Wxpay\WxPayApi;
use App\Http\Controllers\Common\Wxpay\WxPayUnifiedOrder;
use App\Http\Controllers\Common\WXPayConfigController;
use App\Http\Controllers\Common\WXPayController;
use App\Models\Customer\OrderPromotion;
use App\Models\Customer\Promotion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except'=>['createOrder', 'payOrder', 'cancelOrder']]);
    }
    # 消费者用户 订单相关接口处理

    # 商品检测 检测商品列表中是否有商品不属于当前小区
    public function checkPromotions()
    {

    }

    # 计算商品价格
    public function calculate($data,&$items=[])
    {
        $price = 0.0;
        foreach ($data as $key => $val) {
                $item = Promotion::getPromotionPrice($val['id']);
                $items[$val['id']] = $item;
                if(empty($item)) {
                    throw  new \Exception('没有查找到相应活动');
                }
                $price += $item['price']*intval($val['num']);
        }
        return $price;
    }
    # 预生成订单 返回总订单id
    public function preOrder($data)
    {
        $customer = auth()->user();
        # crate order;
        $order = [];
        $order['customerid'] = $customer->id;
        $order['trade_no']   = Order::OrderPrefix.LeaderPromotionController::createOrderSn();
        $order['total']      = $this->calculate($data, $items);
        $order['paytime']    = time();
        $order['status']     = Order::Unpaid;
        $order['note']       = '';
        if($order <= 0) {
            throw new \Exception('订单总价格异常');
        }
        $orderid = Order::createOrder($order);
        # end crateorder
        unset($order);
        $orderpromotion = [];
        # create orderpromotion
        foreach ($data as $key => $val) {
            $val['customerid']  =   $customer->id;
            $val['orderid']     =   $orderid;
            $val['promotionid'] =   $val['id'];
            $val['ordersn']     =   OrderPromotion::OrderPrefix.LeaderPromotionController::createOrderSn();
            $val['num']         =   $val['num'];
            $val['price']       =   $items[$val['id']]['price'];
            $val['total']       =   $val['num']*$items[$val['id']]['price'];
            $val['status']      =   OrderPromotion::Unpaid;
            $val['note']        =   '';
            array_push($orderpromotion, $val);
            unset($val);
        }
        OrderPromotion::createOrderPromotions($orderpromotion);
        # end orderpromotion
        unset($data);
        unset($orderpromotion);
        unset($items);
        return $orderid;
    }
    #  生成订单
    public function createOrder(Request $request)
    {
        #todo 更新库存数量
        try{
            $data = $request->post('data');
            if(!is_array($data)) {
                throw new \Exception('参数错误');
            }
            DB::beginTransaction();
            $this->preOrder($data);
            DB::commit();
            return $this->ok();
        }
        catch (\Exception $exception) {
            DB::rollback();
            return $this->warning($exception->getMessage());
        }
    }

    # 检查订单状态
    public function checkOrder($id)
    {

    }

    #  支付订单 生成预支付参数
    public function payOrder($id)
    {
        # 检查订单是否异常超时
        # 超时更新订单状态
        try{
            if(!($order = Order::checkOrder($id))) {
                try{
                    DB::beginTransaction();
                    Order::cancelCasecadeOrder($id); # 更新订单状态
                    DB::commit();
                }
                catch (\Exception $exception) {
                    DB::rollback();
                    return $this->warning($exception->getMessage());
                }
                throw new \Exception('订单不存在或已超时！');
            }
            $parameters = $this->prePayOrder($order);
            $this->ok($parameters);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    public function prePayOrder($order)
    {
        $customer = auth()->user();
        # 启动微信支付 所需参数
        $jspay  = new WXPayController();
        $config = new WXPayConfigController(); # 支付配置参数
        $input  = new WxPayUnifiedOrder(); #  支付统一下单实例
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no("sdkphp".date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid('openid');
        $wxorder = WxPayApi::unifiedOrder($config, $input); # 执行统一下单
        $parameters = $jspay->GetJsApiParameters($wxorder);
        return $parameters;
    }

    # 取消订单
    public function cancelOrder($id)
    {
        try{
            if(!($order = Order::findOrder($id))) {
                throw new \Exception('订单不存在');
            }
            if($order['status'] != Order::Unpaid) {
                throw new \Exception('只能取消未支付的订单');
            }
            try{
                DB::beginTransaction();
                Order::cancelCasecadeOrder($id);
                DB::commit();
            }
            catch (\Exception $exception) {
                DB::rollback();
                return $this->warning($exception->getMessage());
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    # 订单退款
    public function refundOrder($id)
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








}
