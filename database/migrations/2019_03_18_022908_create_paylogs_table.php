<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaylogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paylogs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customerid')->comment('消费者id');
            $table->string('transaction_id')->comment('微信支付单号');
            $table->string('trade_no')->comment('系统内部单号');
            $table->unsignedDecimal('fee',8,2)->comment('支付金额');
            $table->unsignedTinyInteger('status')->default(0)->comment('订单状态0:已取消 1:未支付 2:已支付');
            $table->string('note')->comment('备注');
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
        Schema::dropIfExists('paylogs');
    }
}
