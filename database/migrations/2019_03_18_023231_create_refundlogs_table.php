<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refundlogs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('customerid')->comment('消费者id');
            $table->string('trade_no')->comment('系统内部退款单号');
            $table->string('refund_id')->comment('微信退款单号');
            $table->unsignedBigInteger('fee')->comment('退款金额');
            $table->unsignedTinyInteger('status')->comment('退款状态 4:退款异常 2退款中 3:退款成功 0:退款失败');
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
        Schema::dropIfExists('refundlogs');
    }
}
