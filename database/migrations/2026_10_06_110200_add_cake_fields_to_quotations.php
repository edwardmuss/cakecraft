<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('occasion_type')->nullable();
            $table->date('occasion_date')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('fulfilment_type')->default('pickup');
            $table->text('delivery_address')->nullable();
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->unsignedBigInteger('cake_size_id')->nullable();
            $table->string('size_label')->nullable();
            $table->string('flavour_name')->nullable();
            $table->string('cake_message')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', fn (Blueprint $t) => $t->dropColumn(['cake_size_id', 'size_label', 'flavour_name', 'cake_message']));
        Schema::table('quotations', fn (Blueprint $t) => $t->dropColumn(['occasion_type', 'occasion_date', 'recipient_name', 'fulfilment_type', 'delivery_address']));
    }
};
