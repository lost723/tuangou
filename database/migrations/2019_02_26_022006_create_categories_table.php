<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('orgid')->comment('关联商户id');
            $table->unsignedBigInteger('parentid')->default(0)->comment('父级id');
            $table->unsignedBigInteger('level')->default(1)->comment('分类层级');
            $table->string('title')->comment('分类名称');
            $table->string('logo')->comment('分类logo');
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
        Schema::dropIfExists('categories');
    }
}
