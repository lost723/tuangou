<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('customerid')->comment('消费者id');
            $table->unsignedBigInteger('orderid')->comment('订单id') ;
            $table->unsignedBigInteger('lpmid')->comment('leader_promotions表的id团长的活动id');
            # 添加商户发布的id 方便核销
            $table->unsignedBigInteger('promotionid')->comment('商家发布的活动id');
            $table->string('ordersn')->comment('系统内部子订单号 可作为退款单号');
            $table->string('refund_id')->comment('微信退款单号');
            $table->unsignedBigInteger('num')->comment('购买数量');
            $table->unsignedBigInteger('price')->comment('购买时活动单价单位分');
            $table->unsignedBigInteger('total')->comment('总价格单位分');
            $table->string('checkcode')->default('')->comment('核销码');
            $table->unsignedInteger('checktime')->default(0)->comment('核销时间');
            $table->unsignedInteger('refundtime')->default(0)->comment('退款时间');
            $table->unsignedInteger('createtime')->default(0)->comment('订单创建时间');
            $table->tinyInteger('status')->comment('订单状态 0:超时异常 1:未支付 2:退款中 3:退款成功 4:退款异常 5:退款关闭 6:支付成功 7:待核销 8:已完成');
            $table->string('note')->default('')->comment('订单备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_promotions');
    }
}
