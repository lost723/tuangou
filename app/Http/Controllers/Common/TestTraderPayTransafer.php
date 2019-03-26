<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Customer\LeaderPromotionController;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * 企业转账测试类
 * Class TraderPayTransafer
 * @package App\Http\Controllers\Common
 */
class TestTraderPayTransafer extends Controller
{
    const TransferPrefix = 600;
    protected $transfer;
    public function __construct()
    {
        $config = config('wechat.payment.transfer');
        $this->transfer = Factory::payment($config)->transfer;
    }

    public function doTransfer()
    {
        # 1、 添加分账方
        //user 类型 姓名 openid
        $users = [];
        $user0 = [
            'type'      =>  'leader',
            'openid'    =>  'oGVme4p11Tsg4mlETN6A2lf4CRd0',
            'name'      =>  '李升贤',
            'desc'      =>  '分佣',
            'ordersn'   =>  self::TransferPrefix.LeaderPromotionController::createOrderSn(),
        ];
        $user1 = [
            'type'      =>  'user',
            'openid'    =>  'oGVme4sHCA-AC5xBcgALmpqoE2gs',
            'name'      =>  'Again',
            'desc'      =>  '提现',
            'ordersn'   =>  self::TransferPrefix.LeaderPromotionController::createOrderSn(),
        ];
        $user2 = [
            'type'      =>  'user',
            'openid'    =>  'oGVme4ns-hkEmnrAGPC-iOVTd7eM',
            'name'      =>  '小二',
            'desc'      =>  '中奖',
            'ordersn'   =>  self::TransferPrefix.LeaderPromotionController::createOrderSn(),
        ];
        $user3 = [
            'type'      =>  'platform',
            'openid'    =>  'oGVme4lNn179wui737CsYFFVurjs',
            'name'      =>  'Hofer',
            'desc'      =>  '分成',
            'ordersn'   =>  self::TransferPrefix.LeaderPromotionController::createOrderSn(),
        ];
//        array_push($users, $user0);
//        array_push($users, $user1);
        array_push($users, $user2);
//        array_push($users, $user3);
        # 2、 todo 做转账记录
        # 3、 进行转账操作
        foreach ($users as $key => $val) {

            # 详见table temptransferrecord
            # 调用接口进行转账功能
           $result =  $this->transfer->toBalance([
                'partner_trade_no'  =>  $val['ordersn'],
                'openid'            =>  $val['openid'],
                'check_name'        =>  'NO_CHECK',
                're_user_name'      =>  $val['name'],
                'desc'              =>  $val['desc'],
                'amount'            =>  100,
            ]);
           $result = $this->transfer->queryBalanceOrder($val['ordersn']);
           dump($result);die;
        }
        # 4、 更新转账记录状态  调用订单查询 最好做个延迟任务 或事件处理

    }
}
