<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->date('date')->nullable()->after('due_amount');
            $table->string('commission_type')->nullable()->after('due_amount');
            $table->double('commission_value')->nullable()->after('due_amount');
            $table->double('total_discount')->nullable()->after('due_amount');
            $table->double('bill')->nullable()->after('due_amount');
            $table->double('total_bill')->nullable()->after('due_amount');
            $table->integer('bank_id')->unsigned()->nullable()->after('due_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['date', 'bank_id', 'commission_type', 'commission_value','total_discount','bill', 'total_bill']);
        });
    }
}
