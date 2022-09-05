<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
           $table->string('order_code')->nullable()->unique()->after('quantity');
           $table->string('commission_type')->nullable()->after('order_code');
           $table->double('commission_value')->nullable()->after('commission_type');
           $table->double('total_discount')->nullable()->after('commission_value');
           $table->double('bill')->nullable()->after('total_discount');
           $table->double('total_bill')->nullable()->after('bill');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_code','commission_type', 'commission_value','total_discount','bill', 'total_bill']);
        });
    }
}
