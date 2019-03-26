<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTemptransferrecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temptransferrecords', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->comment('用户类型 团长 平台 用户');
            $table->string('ordersn')->comment('转账订单号');
            $table->string('payment_no')->comment('企业付款成功，返回的微信付款单号');
            $table->string('openid')->comment('转账到用户零钱的openid');
            $table->string('name')->comment('转账用户姓名 暂时不做真实姓名校验');
            $table->unsignedBigInteger('amount')->comment('转账金额 单位(分)');
            $table->string('desc')->comment('转账原因描述');
            $table->unsignedTinyInteger('status')->comment('转账状态 1:成功  2:失败 根据回传result_code 判断');
            $table->string('remark')->comment('转账回传信息记录或备注');
            $table->unsignedBigInteger('time')->comment('转账发起时间');
            $table->unsignedTinyInteger('payment_time')->comment('企业付款成功时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temptransferrecords');
    }
}
