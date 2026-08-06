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
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->string('listing_type');
            $table->string('fuel_type');
            $table->string('transmission_type');
            $table->string('condition');
            $table->string('kilometer_reading');
            $table->string('make');
            $table->string('model');
            $table->string('min_year');
            $table->string('max_year');
            $table->string('min_price');
            $table->string('max_price');
            $table->string('country_id');
            $table->string('body_type');
            $table->boolean('is_notify')->default(false);
            $table->json('filters')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
