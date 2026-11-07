<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cake details on invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('occasion_type')->nullable();
            $table->date('occasion_date')->nullable();       // date the cake is needed
            $table->string('recipient_name')->nullable();    // celebrant e.g. "Brian"
            $table->string('fulfilment_type')->default('pickup'); // pickup | delivery
            $table->text('delivery_address')->nullable();
            $table->string('source')->default('admin');      // admin | storefront
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('cake_size_id')->nullable()->after('product_id');
            $table->string('size_label')->nullable();
            $table->string('flavour_name')->nullable();
            $table->string('cake_message')->nullable();      // text written on the cake
        });

        // Requests placed on the public storefront, reviewed by admin
        Schema::create('order_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('cake_name');
            $table->foreignId('cake_size_id')->nullable();
            $table->string('size_label')->nullable();
            $table->foreignId('cake_flavour_id')->nullable();
            $table->string('flavour_name')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('estimated_price', 12, 2)->default(0);
            $table->string('occasion_type')->nullable();
            $table->string('recipient_name')->nullable();
            $table->date('needed_on')->nullable();
            $table->string('cake_message')->nullable();
            $table->string('fulfilment_type')->default('pickup');
            $table->text('delivery_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('new'); // new | contacted | converted | declined
            $table->timestamps();
        });

        // A remembered yearly occasion: "Brian's birthday - 14 March"
        Schema::create('customer_occasions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('last_invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->string('occasion_type');                 // birthday, anniversary, graduation...
            $table->string('recipient_name')->nullable();
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('day');
            $table->date('last_occasion_date')->nullable();  // last time we made a cake for it
            $table->string('last_cake')->nullable();         // "Black Forest, 2 kg"
            $table->boolean('reminders_enabled')->default(true);
            $table->timestamps();
            $table->index(['branch_id', 'month', 'day']);
        });

        Schema::create('occasion_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_occasion_id')->constrained()->onDelete('cascade');
            $table->unsignedSmallInteger('year');            // the year the reminder was for
            $table->text('message');
            $table->string('status')->default('sent');       // sent | failed | skipped
            $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['customer_occasion_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occasion_reminders');
        Schema::dropIfExists('customer_occasions');
        Schema::dropIfExists('order_requests');
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['cake_size_id', 'size_label', 'flavour_name', 'cake_message']);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['occasion_type', 'occasion_date', 'recipient_name', 'fulfilment_type', 'delivery_address', 'source']);
        });
    }
};
