<?php

namespace App\Http\Controllers\Customer;


use App\Events\CancelOrderEvent;
use App\Events\CreateOrderEvent;
use App\Http\Resources\Customer\LeaderResource;
use App\Http\Resources\Customer\Order as OrderResource;
use App\Http\Resources\Customer\PickUpStation;
use App\Http\Resources\Customer\SubOrder;
use App\Http\Resources\Customer\SubOrderDetail;
use App\Http\Resources\Customer\UnPaidSubOrderList;
use App\Models\Common\Leader;
use App\Models\Customer\OrderPromotion;
use App\Models\Customer\Promotion;
use App\Models\Customer\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    # 消费者用户 订单相关接口处理

    # todo 检测当前我的订单是否有超时订单
    public function checkOrderTimeout()
    {
        # 检测我的未支付订单中 是否有超时订单
        $customer = auth()->user();
        Order::updateTimeoutOrder($customer->id);
    }

    # 计算商品价格
    public function calculate($data, $total, &$items=[])
    {
        $price = 0.0;
        $customer = auth()->user();
        foreach ($data as $key => $val) {
                $item = (array)Promotion::getPromotion($val['id']);
                # 检测商品列表中是否有商品不属于当前小区
//                if(empty($item)) {
//                    throw  new \Exception('id:'.$val['id'].'没有查找到相应活动');
//                }
                if($item['lid'] != $customer->leaderid) {
                    throw new \Exception("请选择已在团长服务区域内商品，方便您提货!");
                }
                # 库存 订单检测
                if($item['stockable'] && ($item['stock'] < $val['num'])) {
                    throw new \Exception('库存不足');
                }
                if($item['expire'] < time()) {
                    throw new \Exception('活动已下架');
                }
                $items[$val['id']] = $item;
                $price += $item['price'] * $val['num'] ;
        }
        if($price <> $total) {
            throw new \Exception('价格异常');
        }
        return $price;
    }

    # 创建主订单
    public function createMasterOrder($data, $total, $carrier, $mobile, &$items)
    {
        # crate order;
        $customer = auth()->user();
        $order = [];
        $order['customerid'] = $customer->id;
        $order['trade_no']   = Order::OrderPrefix.LeaderPromotionController::createOrderSn();
        $order['total']      = $this->calculate($data, $total, $items);
        $order['createtime'] = time();
        $order['status']     = Order::Unpaid;
        $order['carrier']   = $carrier;
        $order['mobile']    = $mobile;
        $order['note']       = '';
        if($order['total'] <= 0) {
            throw new \Exception('订单总价格异常');
        }
        $orderid = Order::createOrder($order);
        return $orderid;
    }

    # 创建子订单
    public function createSubOrder($data, $orderid, $items)
    {
        $orderpromotion = [];
        $insert = [];
        $customer = auth()->user();
        # create orderpromotion 创建相关子订单
        foreach ($data as $key => $val) {
            $insert['customerid']  =   $customer->id;
            $insert['orderid']     =   $orderid;
            $insert['lpmid']       =   $val['id'];
            $insert['promotionid'] =   $items[$val['id']]['promotionid'];
            $insert['ordersn']     =   OrderPromotion::OrderPrefix.LeaderPromotionController::createOrderSn();
            $insert['num']         =   $val['num'];
            $insert['price']       =   $items[$val['id']]['price'];
            $insert['total']       =   $val['num']*$items[$val['id']]['price'];
            $insert['status']      =   OrderPromotion::Unpaid;
            $insert['note']        =   '';
            $insert['createtime']  =   time();
            array_push($orderpromotion, $insert);
            unset($insert);
            unset($val);
        }
        OrderPromotion::createOrderPromotions($orderpromotion);
    }

    #  生成订单
    public function createOrder(Request $request)
    {
        try{
            $data =  $request->post('data');
            $total = $request->post('total');
            $mobile = $request->post('mobile');
            $carrier = $request->post('carrier');
            $items = [];
            DB::beginTransaction();
            $orderid = $this->createMasterOrder($data, $total, $carrier, $mobile, $items);
            $this->createSubOrder($data, $orderid, $items);
            DB::commit();
            # 触发下单事件
            event(new CreateOrderEvent($orderid));
            $order = Order::findOrder($orderid);
            return $this->okWithResource($order, '成功生成订单');
        }
        catch (\Exception $exception) {
            DB::rollback();
            return $this->warning($exception->getMessage());
        }
    }


    # 取消订单
    public function cancelOrder(Request $request)
    {
        try{
            if(!($order = Order::getOrder($request))) {
                throw new \Exception('订单不存在');
            }
            if($order->status != Order::Unpaid) {
                throw new \Exception('只能取消未支付的订单');
            }
            try{
                DB::beginTransaction();
                Order::cancelCasecadeOrder($order->id);
                DB::commit();
                event(new CancelOrderEvent($order->id));
                return $this->okWithResource([], '成功取消订单');
            }
            catch (\Exception $exception) {
                DB::rollback();
            }
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }



    # 待支付订单
    public function listOrder()
    {
        try{
            $customer = auth()->user();
            $list = Order::getUnpaidList($customer->id);
            $resource =  OrderResource::collection($list);
            return $this->okWithResourcePaginate($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 未支付订单详情
     * @param $id  支付订单id todo 待修改
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderDetail(Request $request)
    {
        try{
            $id = $request->post('id');
            $order = Order::getOrder($request);
            $data = Order::getUnpaidOrderDetail($id);
            $count = $data->count();
            $result['id'] = $order->id;
            $result['createtime'] = $order->createtime;
            $result['expire']     = $order->createtime+Order::TimeOut*60;
            $result['total'] = sprintf("%.2f",$order->total/100);
            $result['trade_no'] = $order->trade_no;
            $result['count'] = $count;
            $result['status'] = $order->status;
            $result['suborder'] = [];
            foreach ($data as $key => $val) {
                array_push($result['suborder'], new UnPaidSubOrderList($val));
            }
            $leaderid = $data->first()->leaderid;
            $result['pickupStation'] = [];
            $leader = new PickUpStation(Leader::find($leaderid));
            array_push($result['pickupStation'], $leader);
            return $this->okWithResource($result);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    # 待发货 待提货  子订单列表
    public function subOrder(Request $request)
    {
        try{
            $customer = auth()->user();
            $list = OrderPromotion::getOrderPromotions($request, $customer->id);
            $resource =  SubOrder::collection($list);
            return $this->okWithResourcePaginate($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    # 子订单订单详情
    public function subOrderDetail(Request $request)
    {
        try{
            $id = $request->post('id');
            $subOrder = OrderPromotion::getOrderPromotionDetail($id);
            $resource = new SubOrderDetail($subOrder);
            return $this->okWithResource($resource);
        }
        catch (\Exception $exception) {
            $this->warning($exception->getMessage());
        }
    }

    # 已完成订单列表
    public function finishedOrder(Request $request)
    {
         # 已完成 + 已取消订单
         try{
             $customer = auth()->user();
             $list = OrderPromotion::getFinishedOrderPromotion($request, $customer->id);
             $resource =  SubOrder::collection($list);
             return $this->okWithResourcePaginate($resource);
         }
         catch (\Exception $exception) {
             return $this->warning($exception->getMessage());
         }
    }


    # 退款订单

    public function  refundOrder(Request $request)
    {
        try{
            $customer = auth()->user();
            $list = OrderPromotion::getRefundOrder($request, $customer->id);
            $resource =  SubOrder::collection($list);
            return $this->okWithResourcePaginate($resource);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }

    }

}
