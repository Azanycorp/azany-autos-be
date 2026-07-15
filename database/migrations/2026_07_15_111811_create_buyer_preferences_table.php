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
        Schema::create('buyer_preferences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->json('vehicle_ids');
            $table->json('fuel_types');
            $table->decimal('budget_min',10,2);
            $table->decimal('budget_max',10,2);
            $table->json('prefered_colors');
            $table->json('transmissions');
            $table->json('body_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_preferences');
    }
};
