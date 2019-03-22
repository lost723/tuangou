<?php

namespace App\Jobs;

use App\Models\Customer\Order;
use App\Models\Customer\OrderPromotion;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckOrderTimeout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $id; # 主订单id
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * 1、检测订单状态
     * 2、如果订单状态为未支付状态
     * 3、检测订单是否超时
     * 4、超时归还 库存+销量 并同时更新订单状态
     * @return void
     */
    public function handle()
    {
        try{
            Log::info("进入CheckOrderTimeout 主订单id:".$this->id);
            $order = Order::find($this->id);
            $oder_promotions = DB::table('order_promotions')->where('orderid', $this->id)->get(['promotionid', 'lpmid', 'num']);
            if($order->status <> Order::Unpaid) {
                # 订单 不属于检测范围
                Log::info('主订单id：'.$this->id." 订单号:".$order->trade_no."的订单状态".$order->status);
                return ;
            }

            # todo 警报设置
            # 订单超时
            try{
                DB::beginTransaction();
                # 更新订单状态
                # 增加库存 减少销量
                DB::table('orders')->where('id', $this->id)->update(['status'=>Order::Cancel]);
                DB::table('order_promotions')->where('orderid', $this->id)->update(['status'=>OrderPromotion::Expire]);
                foreach($oder_promotions as $key=>$val) {
                    $result = DB::table('leader_promotions')->where('id', $val->lpmid)->decrement('sales', $val->num);
                    $promotions = DB::table('promotions')->where('id', $val->promotionid)->first();
                    DB::table('promotions')->where('id', $val->promotionid)->where('stockable', 1)->decrement('sales', $val->num);
                    if($promotions->stockable) {
                        DB::table('promotions')->where('id', $val->promotionid)->increment('stock', $val->num);
                    }
                }
                DB::commit();
                Log::info("成功退出CheckOrderTimeout");
            }
            catch (\Exception $exception) {
                # todo 写日志
                DB::rollback();
                Log::info($exception->getMessage());
            }


        }
        catch (\Exception $exception) {

            Log::info($exception->getMessage());
        }
    }
}
