<?php

namespace App\Http\Controllers\Customer;


use App\Http\Resources\Customer\OrderItem;
use App\Http\Resources\Customer\LeaderResource;
use App\Http\Resources\Customer\Order as OrderResource;
use App\Http\Resources\Customer\SubOrder;
use App\Http\Resources\Customer\SubOrderDetail;
use App\Models\Auth\Customer;
use App\Models\Common\Leader;
use App\Models\Customer\LeaderPromotion;
use App\Models\Customer\OrderPromotion;
use App\Models\Customer\Promotion;
use App\Models\Customer\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected  $customer;
    public function __construct()
    {   # todo 默认第一个用户
        # $this->customer = auth()->user();
        $this->customer = Customer::find(1);
    }
    # todo 未设置库存处理
    # 消费者用户 订单相关接口处理

    # todo 订单超时检测 并更新订单状态
    public function checkOrderTimeout()
    {

    }


    # 计算商品价格
    public function calculate($data, $total, &$items=[])
    {
        $price = 0.0;
        foreach ($data as $key => $val) {
                $item = (array)Promotion::getPromotion($val['id']);
                #检测商品列表中是否有商品不属于当前小区
                if(empty($item)) {
                    throw  new \Exception('id:'.$val['id'].'没有查找到相应活动');
                }
                if($item['commid'] != $this->customer->commid) {
                    throw new \Exception("请选择已绑定小区附近的商家，方便您提货!");
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
    public function createMasterOrder($data, $total, &$items)
    {
        # crate order;
        $order = [];
        $order['customerid'] = $this->customer->id;
        $order['trade_no']   = Order::OrderPrefix.LeaderPromotionController::createOrderSn();
        $order['total']      = $this->calculate($data, $total, $items);
        $order['createtime'] = time();
        $order['status']     = Order::Unpaid;
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
        # create orderpromotion 创建相关子订单
        foreach ($data as $key => $val) {
            $insert['customerid']  =   $this->customer->id;
            $insert['orderid']     =   $orderid;
            $insert['lpmid']       =   $val['id'];
            $insert['promotionid'] =   $items[$val['id']]['promotionid'];
            $insert['ordersn']     =   OrderPromotion::OrderPrefix.LeaderPromotionController::createOrderSn();
            $insert['num']         =   $val['num'];
            $insert['price']       =   $items[$val['id']]['price'];
            $insert['total']       =   $val['num']*$items[$val['id']]['price'];
            $insert['status']      =   OrderPromotion::Unpaid;
            $insert['note']        =   '';
            array_push($orderpromotion, $insert);
            unset($insert);
            unset($val);
        }
        OrderPromotion::createOrderPromotions($orderpromotion);
    }

    #  生成订单
    public function createOrder(Request $request)
    {
        #todo 更新库存数量
        try{
            $data =  $request->post('data');
            $total = $request->post('total');
            $items = [];
            DB::beginTransaction();
            $orderid = $this->createMasterOrder($data, $total, $items);
            $this->createSubOrder($data, $orderid, $items);
            DB::commit();
            $order = Order::findOrder($orderid);
            return $this->ok($order);
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
                return $this->ok();
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



    # 待支付订单
    public function listOrder()
    {
        try{
            $list = Order::getUnpaidList($this->customer->id);
            return OrderResource::collection($list);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }

    /**
     * 未支付订单详情
     * @param $id  支付订单id
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderDetail(Request $request)
    {
        try{
            $id = $request->post('id');
            $order = Order::getOrder($request);
            $data = Order::getUnpaidOrderDetail($id);
            $count = $data->count();
            $data = $data->groupBy('leaderid');
            $result['id'] = $order->id;
            $result['createtime'] = $order->createtime;
            $result['total'] = $order->total;
            $result['trade_no'] = $order->trade_no;
            $result['count'] = $count;
            $result['status'] = $order->status;
            $result['order'] = [];
            foreach ($data as $key => $val) {
                $tmp = [];
                foreach ($val as $k => $v) {
                    if(!array_key_exists('leader', $tmp)) {
                        $tmp['leader'] = [];
                        $leader = new LeaderResource(Leader::find($v->leaderid));
                        array_push($tmp['leader'], $leader);
                    }
                    if(!array_key_exists('items', $tmp)) {
                        $tmp['items'] = [];
                    }
                    array_push($tmp['items'], new OrderItem($v));
                }
                array_push($result['order'], $tmp);
            }
            return $this->ok(['data'=>$result]);
        }
        catch (\Exception $exception) {
            return $this->warning($exception->getMessage());
        }
    }


    # 全部订单 待收货订单 已完成
    public function subOrder(Request $request)
    {
        try{
            $list = OrderPromotion::getOrderPromotions($request);
            return SubOrder::collection($list);
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
            return new SubOrderDetail($subOrder);
        }
        catch (\Exception $exception) {
            $this->warning($exception->getMessage());
        }
    }

}
