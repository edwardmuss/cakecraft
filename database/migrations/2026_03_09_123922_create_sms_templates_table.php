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
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('trigger_name'); // e.g., 'order_created', 'payment_received', 'delivered'
            $table->text('message'); // The template with placeholders like {CustomerName}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // A branch should only have one template per trigger
            $table->unique(['branch_id', 'trigger_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
    }
};
