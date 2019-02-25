<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orderid')->comment('orders表中的订单id');
            $table->unsignedInteger('order_promotionid')->comment('orderpromotions表中的订单id');
            $table->string('refund_no')->comment('系统内部退款单号');
            $table->string('refund_id')->comment('微信退款单号');
            $table->decimal('total',10,2)->comment('原订单总额');
            $table->decimal('refund',10,2)->comment('退款总额');
            $table->tinyInteger('status')->comment('退款订单状态 0:退款异常 1: 退款中 2:退款成功');
            $table->string('note')->comment('退款备注');
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
        Schema::dropIfExists('refunds');
    }
}
