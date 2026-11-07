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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('expense_category_id')->constrained()->onDelete('restrict');

            $table->date('expense_date');
            $table->string('reference_number')->nullable(); // Receipt or Invoice # from supplier

            // QuickBooks Style Financials
            $table->decimal('amount_before_tax', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2); // amount_before_tax + tax_amount

            $table->string('payment_method')->nullable(); // Cash, M-Pesa, Bank Transfer
            $table->text('notes')->nullable();

            // Optional: File upload for receipt images
            $table->string('receipt_path')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
