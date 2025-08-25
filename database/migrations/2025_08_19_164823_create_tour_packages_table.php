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
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->String('title');
            $table->unsignedInteger('total_days');
            $table->longText('description');
            $table->String('main_image')->nullable();
            $table->String('sub_image1')->nullable();
            $table->String('sub_image2')->nullable();
            $table->String('sub_image3')->nullable();
            $table->String('sub_image4')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};
