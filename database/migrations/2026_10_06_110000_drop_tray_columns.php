<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'customers'       => ['tray_preference'],
        'invoice_items'   => ['tray_count', 'tray_type'],
        'quotation_items' => ['tray_count', 'tray_type'],
        'invoices'        => ['returned_trays'],
    ];

    public function up(): void
    {
        foreach ($this->columns as $table => $cols) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $existing = array_values(array_filter($cols, fn ($c) => Schema::hasColumn($table, $c)));
            if ($existing) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn($existing));
            }
        }
    }

    public function down(): void
    {
        Schema::table('customers', fn (Blueprint $t) => $t->string('tray_preference')->default('none'));
        Schema::table('invoice_items', function (Blueprint $t) {
            $t->integer('tray_count')->default(0);
            $t->string('tray_type')->nullable();
        });
        Schema::table('quotation_items', function (Blueprint $t) {
            $t->integer('tray_count')->default(0);
            $t->string('tray_type')->nullable();
        });
        Schema::table('invoices', fn (Blueprint $t) => $t->integer('returned_trays')->default(0));
    }
};
