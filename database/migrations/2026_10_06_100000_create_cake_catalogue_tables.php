<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Collections = categories / occasions (Wedding, Birthday, Anniversary, Graduation...)
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            // When set, orders from this collection create a yearly "occasion" for reminders
            $table->string('occasion_type')->nullable(); // birthday, wedding, anniversary, graduation...
            $table->boolean('is_recurring')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'slug']);
        });

        Schema::create('cake_flavours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cake_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('label');                       // "1 kg", "2 tier", "6-inch"
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->string('serves')->nullable();          // "8-10"
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('short_description')->nullable();
            // signature = flavour-named cake priced by size, custom = designer/themed cake, cupcake, mini
            $table->string('product_type')->default('custom');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true); // show on storefront
            $table->unsignedSmallInteger('lead_time_days')->default(2);
        });

        // Size price grid: each cake has a price per size
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('cake_size_id')->constrained('cake_sizes')->onDelete('cascade');
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'cake_size_id']);
        });

        // Flavours offered on a cake, with optional surcharge
        Schema::create('product_flavours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('cake_flavour_id')->constrained('cake_flavours')->onDelete('cascade');
            $table->decimal('extra_price', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'cake_flavour_id']);
        });

        Schema::create('collection_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unique(['collection_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('product_flavours');
        Schema::dropIfExists('product_sizes');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'image', 'gallery', 'short_description', 'product_type', 'is_featured', 'is_published', 'lead_time_days']);
        });
        Schema::dropIfExists('cake_sizes');
        Schema::dropIfExists('cake_flavours');
        Schema::dropIfExists('collections');
    }
};
