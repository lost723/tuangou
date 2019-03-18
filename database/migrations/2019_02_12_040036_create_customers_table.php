<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
//            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('avatar');
            /**
             * @date 2019-2-13
             * 添加所需字段
             */
            $table->string('openid', 50)->unique();
            $table->string('unionid', 50)->unique();
            $table->string('nickname', 20);
            $table->boolean('gender')->comment('0:女 1:男')->default(0);
            $table->string('mobile', 20)->default('');
            $table->string('country', 20);
            $table->string('province', 20);
            $table->string('city', 20);

            $table->unsignedInteger('leaderid')->default(0)->comment('绑定团长id');



            $table->rememberToken();
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
        Schema::dropIfExists('customers');
    }
}
