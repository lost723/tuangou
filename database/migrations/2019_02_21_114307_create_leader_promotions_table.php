<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaderPromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   # todo  添加
        Schema::create('leader_promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('leaderid')->comment('团长id');
            $table->unsignedInteger('promotionid')->comment('活动id');
            $table->unsignedInteger('sales')->default(0)->comment('该商品销量');
            $table->string('ordersn')->comment('订单号');
            $table->unsignedTinyInteger('active')->comment('团长挑选货物状态 0:取消  1:挑选');
            $table->unsignedTinyInteger('status')->default(0)->comment('0:未签收 1:已签收 2:已核销完成');
            $table->string('note')->default('')->comment('订单异常备注');
            /*
                数据统计字段
                实际 签收数量     checktotal     实际核销数量      verifycount
                活动分享次数      sharecount     浏览次数         viewcount
                支付次数         paycount       未支付次数       unpaycount
                取消次数         cancelcount    加入购物车次数    cargoscount
                退款次数        refundcount
            */
            $table->unsignedInteger('checkcount')->default(0)->comment('实际签收数量');
            $table->unsignedInteger('verifycount')->default(0)->comment('实际数量');
            $table->unsignedInteger('sharecount')->default(0)->comment('分享数量');
            $table->unsignedInteger('viewcount')->default(0)->comment('浏览数量');
            $table->unsignedInteger('paycount')->default(0)->comment('支付数量');
            $table->unsignedInteger('cargoscount')->default(0)->comment('加入购物车数量');
            $table->unsignedInteger('refundcount')->default(0)->comment('已退款数量');
            $table->unsignedInteger('unpaycount')->default(0)->comment('未支付数量');

            $table->unsignedBigInteger('checktime')->default(0)->comment('签收时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leader_promotions');
    }
}
