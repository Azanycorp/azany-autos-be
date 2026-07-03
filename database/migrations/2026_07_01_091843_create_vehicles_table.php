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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('slug')->unique();
            $table->string('listing_type');
            $table->integer('auction_days')->default(0);
            $table->string('auction_duration')->nullable();
            $table->string('status')->default('pending');
            $table->bigInteger('country_id')->unsigned();
            $table->string('city');
            $table->string('fuel_type');
            $table->string('transmission_type');
            $table->string('condition');
            $table->string('kilometer_reading');
            $table->string('engine_capacity');
            $table->string('previous_owner')->nullable();
            $table->string('make');
            $table->string('model');
            $table->string('year');
            $table->string('variant')->nullable();
            $table->string('body_type');
            $table->string('vin')->unique();
            $table->string('accident_history');
            $table->string('damage_history');
            $table->text('service_history')->nullable();
            $table->text('front_image');
            $table->text('back_image');
            $table->text('rear_image');
            $table->text('passenger_side_image');
            $table->text('dashboard_image');
            $table->text('video_link')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->json('features');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
