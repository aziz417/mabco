<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_products', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->unsigned();
            $table->integer('retailer_id')->unsigned();
            $table->string('type')->nullable();
            $table->integer('return_code')->nullable();
            $table->tinyInteger('approve')->nullable();
            $table->double('total_amount')->nullable();
            $table->string('commission_type')->nullable();
            $table->double('commission_value')->nullable();
            $table->double('discount')->nullable();
            $table->double('return_amount')->nullable();
            $table->string('date')->nullable();
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
        Schema::dropIfExists('return_products');
    }
}
