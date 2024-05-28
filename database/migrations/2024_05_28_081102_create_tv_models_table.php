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
        Schema::create('tv_models', static function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price');
            $table->string('link')->nullable()->default(null);
            $table->string('image')->nullable()->default(null);
            $table->foreignId('brand_id')->references('id')->on('tv_brands');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_models');
    }
};
