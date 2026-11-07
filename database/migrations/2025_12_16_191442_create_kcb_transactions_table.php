<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKcbTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kcb_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->string('request_id')->nullable();
            $table->string('channel_code')->nullable();
            $table->string('timestamp')->nullable();
            $table->decimal('transaction_amount', 15, 2);
            $table->string('currency')->nullable();
            $table->string('customer_reference')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_mobile_number')->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->string('narration')->nullable();
            $table->string('credit_branch_identifier')->nullable();
            $table->string('organization_short_code')->nullable();
            $table->string('till_number')->nullable();
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
        Schema::dropIfExists('kcb_transactions');
    }
}
