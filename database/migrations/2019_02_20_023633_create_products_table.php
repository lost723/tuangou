<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('catid')->comment('分类id');
            $table->unsignedInteger('orgid')->comment('商户id');
            $table->unsignedBigInteger('optid')->comment('操作人id');
            $table->unsignedInteger('mtpd')->comment('售后维保期限，单位：天');
            $table->unsignedBigInteger('distid')->comment('配送区域模版id');
            $table->unsignedInteger('issue')->default(0)->comment('活动进行了几期');
            $table->string('title')->comment('商品名称');
            $table->string('norm')->comment('商品规格');
            $table->string('intro')->comment('商品简介');
            $table->string('thumb')->comment('商品列表图');
            $table->string('picture')->comment('商品详情头部大图片');
            $table->string('content')->comment('商品详情富文本');
            $table->unsignedInteger('rate')->comment('团长佣金返点');
            $table->unsignedInteger('price')->comment('商品售价');
            $table->unsignedInteger('quotation')->comment('商品市场价');
            $table->tinyInteger('status')->default(9)->comment('默认9: 正常， 1:下架, 0:删除');
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
        Schema::dropIfExists('products');
    }
}
