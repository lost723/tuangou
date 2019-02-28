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
    {
        Schema::create('leader_promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('leaderid')->comment('团长id');
            $table->unsignedInteger('promotionid')->comment('活动id');
            $table->unsignedInteger('num')->default(0)->comment('挑选该商品数量');
            $table->unsignedInteger('sales')->default(0)->comment('该商品销量');
            $table->string('ordersn')->comment('订单号');
            $table->string('check')->default('')->comment('核销码');
            $table->string('note')->default('')->comment('订单异常备注');
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
        Schema::dropIfExists('leader_promotions');
    }
}
