<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::create('stores', function (Blueprint $table) {
//            //
//            $table->increments('id');
//            $table->string('name')->comment('店铺名称');
//            $table->string('logo')->nullable()->comment('店铺logo');
//            $table->string('contact')->comment('店主姓名');
//            $table->string('idcard')->comment('店主身份证件号码');
//            $table->string('mobile')->comment('店主手机号');
//            $table->string('idcard_front_url')->comment('身份证件正面照url');
//            $table->string('idcard_back_url')->comment('身份证件反面照url');
//            $table->string('store_license_url')->comment('营业执照url');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('stores');
    }
}
