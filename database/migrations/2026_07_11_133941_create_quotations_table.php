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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');

            $table->string('quote_number')->unique();
            $table->date('quote_date');
            $table->date('expiry_date')->nullable();
            $table->string('subject')->nullable(); // e.g., "Website Development"

            // Financials
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            // Status tracking
            $table->enum('status', ['draft', 'sent', 'accepted', 'declined', 'invoiced'])->default('draft');

            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable(); // Internal notes

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
