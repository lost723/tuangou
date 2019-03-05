<?php

namespace App\Http\Controllers\Weixin;

use App\Http\Controllers\Weixin\Wxpay\WxPayApi;
use App\Http\Controllers\Weixin\Wxpay\WxPayException;
use App\Http\Controllers\Weixin\Wxpay\WxPayJsApiPay;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WXPayController extends Controller
{

     /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }

        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);

        $config = new WXPayConfigController();
        $jsapi->SetPaySign($jsapi->MakeSign($config));
//        $parameters = json_encode($jsapi->GetValues());
        return $jsapi->GetValues();
    }







}
