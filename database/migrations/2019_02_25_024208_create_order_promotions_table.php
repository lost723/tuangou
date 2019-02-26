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
            $table->unsignedInteger('orderid')->comment('订单id');
            $table->unsignedInteger('promotionid')->comment('leader_promotions表的id');
            $table->string('ordersn')->comment('当前活动的订单号');
            $table->unsignedInteger('num')->comment('购买数量');
            $table->decimal('price',8,2)->comment('购买时活动单价');
            $table->decimal('total',8,2)->comment('总价格');
            $table->string('checkCode')->comment('核销码');
//            $table->string('checkUrl')->comment('核销url');
            $table->tinyInteger('status')->comment('订单状态 0:超时异常 1:未支付 2:已退款 3:已支付待收货 4:订单完成');
            $table->string('note')->comment('订单备注');
            $table->timestamps();
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
