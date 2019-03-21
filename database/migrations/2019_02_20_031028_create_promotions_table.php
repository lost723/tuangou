<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('orgid')->comment('商户id');
            $table->unsignedBigInteger('optid')->comment('操作人id');
            $table->unsignedBigInteger('productid')->comment('产品id');
            $table->unsignedBigInteger('price')->comment('活动售价单位分');
            $table->unsignedBigInteger('quotation')->comment('活动售价单位分');

            $table->unsignedInteger('start')->comment('活动开始时间');
            $table->unsignedInteger('expire')->comment('活动截止时间');
            $table->unsignedInteger('deliveryday')->comment('配送日期，时间戳');
            $table->unsignedInteger('aftersale')->comment('售后截止日期,时间戳');
            $table->unsignedInteger('stock')->default(0)->comment('库存量，stockable=1时有效');
            $table->unsignedInteger('stockable')->default(0)->comment('需要库存=1； 默认不需要库存=0');
            $table->unsignedInteger('sales')->default(0)->comment('卖出去的数量');
            $table->unsignedInteger('leaders')->default(0)->comment('参与的团长数量');
            $table->unsignedTinyInteger('status')->default(0)->comment('0:未发布 1：进行中 2：备货中 3：配送中 4：已签收 9：结束');
            $table->unsignedInteger('version')->default(0)->comment('最后修改的时间戳，用于防止同时修改的冲突');
            # 统计字段
            $table->unsignedInteger('checkcount')->default(0)->comment('实际签收数量');
            $table->unsignedInteger('sharecount')->default(0)->comment('分享数量');
            $table->unsignedInteger('viewcount')->default(0)->comment('浏览数量');
            $table->unsignedInteger('paycount')->default(0)->comment('支付数量');
            $table->unsignedInteger('cargoscount')->default(0)->comment('加入购物车数量');
            $table->unsignedInteger('refundcount')->default(0)->comment('已退款数量');
            $table->unsignedInteger('unpaycount')->default(0)->comment('未支付数量');
            
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
        Schema::dropIfExists('promotions');
    }
}
