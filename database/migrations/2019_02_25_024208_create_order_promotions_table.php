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
            $table->unsignedInteger('customerid')->comment('消费者id');
            $table->unsignedInteger('orderid')->comment('订单id') ;
            $table->unsignedInteger('lpmid')->comment('leader_promotions表的id团长的活动id');
            # 添加商户发布的id 方便核销
            $table->unsignedBigInteger('promotionid')->comment('商家发布的活动id');
            # todo 添加商品id 便于检索用户购买记录
            $table->string('ordersn')->comment('当前活动的订单号');
            $table->string('refund_id')->comment('微信退款单号');
            $table->unsignedInteger('num')->comment('购买数量');
            $table->decimal('price',8,2)->comment('购买时活动单价');
            $table->decimal('total',8,2)->comment('总价格');
            $table->string('checkcode')->default('')->comment('核销码');
            $table->unsignedInteger('checktime')->default(0)->comment('核销时间');
            $table->unsignedInteger('refundtime')->default(0)->comment('退款时间');
//            $table->string('checkUrl')->comment('核销url');
            $table->tinyInteger('status')->comment('订单状态 0:超时异常 1:未支付 2:已退款 3:已支付待收货 4:订单完成');
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
