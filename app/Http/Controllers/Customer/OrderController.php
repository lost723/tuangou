<?php

namespace App\Http\Controllers\Customer;

use App\Customer\Models\Order;
use App\Http\Controllers\Common\Wxpay\WxPayApi;
use App\Http\Controllers\Common\Wxpay\WxPayRefund;
use App\Http\Controllers\Common\Wxpay\WxPayUnifiedOrder;
use App\Http\Controllers\Common\WXPayConfigController;
use App\Http\Controllers\Common\WXPayController;
use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\OrderPromotion;
use App\Models\Customer\Promotion;
use App\Models\Customer\RefundOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    # 用户订单相关页面
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
            if(!$this->checkPromotions()) {
                throw new \Exception('请选择已绑定小区附近的商家，方便您提货!');
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


    /**
     * 订单退款
     * @param $id   id为用户所购买的某个商品的订单id
     * @return \Illuminate\Http\JsonResponse
     */
    public function refundOrder($id)
    {
        try{
            # 查询订单 (已支付 且 活动未结束)
            if(!($order = OrderPromotion::checkOrderPromotions($id))) {
                throw new \Exception('该商品所参与的活动已结束,请勿退款！');
            }
            # doRefund 创建退款单
            if($refundArgs = $this->createRefund($order)) {
                throw new \Exception('退款订单创建失败');
            }
            # 发起退款请求
            if($data = $this->doRefund($refundArgs)) {
                # 更新退款表状态 订单状态
                # check result_code refund_fee #update refund_id status
                if($data['result'] != 'SUCCESS') {
                    throw new \Exception($data['err_code'].':'.$data['err_code_des']);
                }
                $refundOrder = RefundOrder::findOrderByRefundNo($data['out_refund_no']);
                if(($refundOrder['status'] == RefundOrder::Refunding) && ($refundOrder['refund'] == ($data['refund_fee'] / 100))) {
                    # update 退款表 订单状态表
                    $updateArr = [];
                    $updateArr['refund_id'] = $data['refund_id'];
                    $updateArr['status'] = RefundOrder::Refunding;
                    try{
                        DB::beginTransaction();
                        # 更新退款单状态
                        RefundOrder::updateRefund($updateArr, $refundOrder['id']);
                        # 更新用户订单状态为已退款
                        DB::table('order_promotions')
                            ->where('id',$refundOrder['order_promotionid'])
                            ->update(['status' => OrderPromotion::Refunding]);
                        OrderPromotion::findOrderById();
                        DB::commit();
                    }
                    catch (\Exception $exception){
                        DB::rollback();
                        return $this->warning($exception->getMessage());
                    }
                }
                else {
                    # 订单异常
                }
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }




    # 创建退款订单
    public function createRefund($data)
    {
        $customer = auth()->user();
        $record = [];
        $record['customerid']            = $customer->id;
        $record['orderid']               = $data['orderid'];
        $record['order_promotionid']     = $data['order_promotionid'];
        $record['trade_no']              = $data['trade_no'];
        $record['transaction_id']        = $data['transaction_id'];
        $record['total']                 = $data['total'];
        $record['refund']                = $data['refund'];
        $record['refund_no']             = RefundOrder::RefundPrefix.LeaderPromotionController::createOrderSn();
        $record['status']                = RefundOrder::Refunding;
        $record['note']                  = $data['note'];
        # 生成退款订单
        if(RefundOrder::createRefund($record, $record['order_promotionid'])) {
            return $record;
        }
        else {
            return false;
        }
    }

    # doRefund 发起退款请求
    public function doRefund($data)
    {
        $input = new WxPayRefund();
        $input->SetTransaction_id($data['transaction_id']);
        $input->SetTotal_fee($data['total']*100);
        $input->SetRefund_fee($data['refund']*100);

        $config = new WXPayConfigController();
        $input->SetOut_refund_no($data['refund_no']);
        $input->SetOp_user_id($config->GetMerchantId());
        return WxPayApi::refund($config, $input);
    }

    # 订单列表
    # 全部订单 待支付订单  待收货订单 已完成
    public function listOrder()
    {

    }

    # 订单详情
    public function detailOrder($id)
    {

    }








}
