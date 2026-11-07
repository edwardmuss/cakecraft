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
        // 1. Update Customers
        Schema::table('customers', function (Blueprint $table) {
            $table->string('organization_name')->nullable()->after('email');
            $table->string('delivery_location')->nullable()->after('billing_address');
            // Customer Preference (Do they use Disposable or Recyclable?)
            $table->enum('tray_preference', ['none', 'disposable', 'recyclable'])->default('none')->after('delivery_location');
        });

        // 2. Update Invoices (for Terms/Notes)
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('due_date');
            // Change from enum to string, set 'processing' as the new default
            $table->string('status')->default('processing')->change();
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->integer('tray_count')->default(0)->after('quantity');
            $table->string('tray_type')->nullable()->after('tray_count'); // 'dis' or 'rec'
        });
        Schema::table('branches', function (Blueprint $table) {
            $table->text('payment_details')->nullable()->after('vat_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('tray_preference');
            $table->dropColumn(['organization_name', 'delivery_location']);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'partially_paid', 'paid', 'delivered', 'cancelled'])->default('pending')->change();
            $table->dropColumn('notes');
        });
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['tray_count', 'tray_type']);
        });
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('payment_details');
        });
    }
};
