<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('fee', 10, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('order_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_zone_id')->nullable();
            $table->string('delivery_zone_name')->nullable();
            $table->decimal('delivery_fee', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('order_requests', fn (Blueprint $t) => $t->dropColumn(['delivery_zone_id', 'delivery_zone_name', 'delivery_fee']));
        Schema::dropIfExists('delivery_zones');
    }
};
