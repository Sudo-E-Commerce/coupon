<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('code')->unique();
            $table->tinyInteger('type')->default(0); // % or money
            $table->integer('max_value')->default(0); // max value when use coupon
            $table->integer('value')->default(0);
            $table->integer('select')->default(0); // type = 0: all, type = 1: categories, type = 2: products
            $table->integer('limit')->default(1);
            $table->integer('used')->default(1);
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();            
        });

        Schema::create('coupon_selects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('type_id');
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
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('coupon_selects');
    }
}
