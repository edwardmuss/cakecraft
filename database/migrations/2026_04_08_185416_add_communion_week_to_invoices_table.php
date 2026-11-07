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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('communion_month', 7)->nullable()->after('due_date'); // Format: YYYY-MM
            $table->tinyInteger('communion_week')->nullable()->after('communion_month'); // 1, 2, 3, 4, 5
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['communion_month', 'communion_week']);
        });
    }
};
