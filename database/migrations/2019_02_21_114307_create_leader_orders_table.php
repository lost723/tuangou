<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaderOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leader_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('leaderid')->comment('团长id');
            $table->unsignedInteger('promotionid')->comment('活动id');
            $table->unsignedInteger('productid')->comment('商品id');
            $table->unsignedInteger('num')->default(0)->comment('挑选该商品数量');
            $table->unsignedInteger('sales')->default(0)->comment('该商品销量');
            $table->unsignedInteger('expire')->comment('活动截止时间');
            $table->string('check')->comment('核销码');
            $table->unsignedTinyInteger('status')->comment('-1:异常 1:进行中 2:配送中 0:已签收 ');
            $table->string('note')->comment('订单异常备注');
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
        Schema::dropIfExists('leader_orders');
    }
}
