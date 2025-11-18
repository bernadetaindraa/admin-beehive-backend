<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('wingspan')->nullable();
            $table->string('flight_endurance')->nullable();
            $table->string('flight_range')->nullable();
            $table->string('flight_height')->nullable();
            $table->text('other_details')->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->json('images')->nullable();
            $table->json('include_items')->nullable();
            $table->json('package_options')->nullable();
            $table->json('financing')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};