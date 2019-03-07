<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerid')->comment('消费者id');
            $table->string('trade_no')->comment('用于微信支付的内部订单号');
            $table->string('transaction_id')->default('')->comment('微信支付的交易订单号');
            $table->decimal('total',8,2)->comment('支付总价格');
            $table->unsignedInteger('createtime')->comment('下单时间');
            $table->unsignedInteger('paytime')->comment('支付时间');
            $table->tinyInteger('status')->comment('订单状态 0:取消 1:未支付 2:已支付');
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
        Schema::dropIfExists('orders');
    }
}

