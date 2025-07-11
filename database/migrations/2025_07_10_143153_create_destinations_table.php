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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string("location");
            $table->decimal("latitude", 10, 8)->nullable();
            $table->decimal("longitude", 11, 8)->nullable();
            $table->enum("category", ["Alam", "Sejarah", "Petualangan", "Kuliner", "Santai"])->default("Alam")->nullable();
            $table->decimal("average_rating", 2, 1)->nullable();
            $table->string("image_url")->nullable();
            $table->string("approx_price_range")->nullable();
            $table->string("best_time_to_visit")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
