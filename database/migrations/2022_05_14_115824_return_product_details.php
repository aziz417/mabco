<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReturnProductDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_product_details', function (Blueprint $table) {
            $table->id();
            $table->integer('return_product_id')->unsigned();
            $table->string('order_code')->nullable();
            $table->integer('brand_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->double('product_price')->nullable();
            $table->integer('product_return_reason_id')->unsigned();
            $table->integer('order_quantity')->nullable();
            $table->integer('return_quantity')->nullable();
            $table->double('sub_total_price')->nullable();
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
        Schema::dropIfExists('return_product_details');
    }
}
