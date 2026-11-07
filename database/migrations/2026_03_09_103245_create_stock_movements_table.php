<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Who made the manual change

            $table->enum('type', ['addition', 'deduction', 'sale', 'adjustment']);
            $table->integer('quantity'); // The amount moved (e.g., 5)
            $table->integer('balance_after'); // Running balance for easy auditing
            $table->string('reference')->nullable(); // e.g., "Invoice #INV-00001" or "New Stock Delivery"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
