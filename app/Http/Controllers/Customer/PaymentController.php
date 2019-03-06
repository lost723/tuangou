<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Weixin\Wxpay\WxPayApi;
use App\Http\Controllers\Weixin\Wxpay\WxPayRefund;
use App\Http\Controllers\Weixin\Wxpay\WxPayUnifiedOrder;
use App\Http\Controllers\Weixin\WXPayConfigController;
use App\Http\Controllers\Weixin\WXPayController;

class PaymentController extends Controller
{

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

    # 准备支付参数
    public function prePayOrder($order)
    {   # todo customer 授权默认用户
//        $customer = auth()->user();
        $customer = Customer::find(1);
        # 启动微信支付 所需参数
        $jspay  = new WXPayController();
        $config = new WXPayConfigController(); # 支付配置参数
        $input  = new WxPayUnifiedOrder(); #  支付统一下单实例
        $input->SetBody($order['trade_no']);
        $input->SetOut_trade_no($order['trade_no']);
        $input->SetTotal_fee(($order['total']*100));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($customer['openid']);
        $wxorder = WxPayApi::unifiedOrder($config, $input); # 执行统一下单
        $parameters = $jspay->GetJsApiParameters($wxorder);
        return $parameters;
    }
}
