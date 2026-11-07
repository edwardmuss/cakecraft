<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name');
            $table->string('branch_email')->unique();
            $table->string('company_phone')->unique();
            $table->string('kra_pin_number')->unique()->nullable();
            $table->double('vat_percentage')->default(16.00);
            $table->text('about')->nullable();
            $table->string('website')->nullable();
            $table->string('company_logo')->default('avatar.png');
            $table->string('branch_cover_image')->default('cover_image.jpg');
            $table->boolean('locked')->default(false);
            $table->string('last_ip')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });

        // Create an account after the table is created
        Branch::create([
            'branch_name' => 'Cakecraft Pastry Academy',
            'branch_email' => 'edwardmuss5@gmail.com',
            'company_phone' => '254722222898',
            "isActive" => true,
            'kra_pin_number' => '',
            'about' => 'Cakes and pastry',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
