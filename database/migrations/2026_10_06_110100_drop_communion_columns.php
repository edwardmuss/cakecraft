<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $existing = array_values(array_filter(
            ['communion_date', 'communion_month', 'communion_week'],
            fn ($c) => Schema::hasColumn('invoices', $c)
        ));
        if ($existing) {
            Schema::table('invoices', fn (Blueprint $t) => $t->dropColumn($existing));
        }
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $t) {
            $t->date('communion_date')->nullable();
            $t->string('communion_month')->nullable();
            $t->unsignedTinyInteger('communion_week')->nullable();
        });
    }
};
