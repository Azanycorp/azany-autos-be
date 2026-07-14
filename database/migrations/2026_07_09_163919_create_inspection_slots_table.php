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
        Schema::create('inspection_slots', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('dealer_id')->unsigned();
            $table->bigInteger('buyer_id')->nullable()->unsigned();
            $table->bigInteger('vehicle_id')->unsigned();
            $table->bigInteger('location_id')->unsigned();
            $table->date('inspection_date');
            $table->string('inspection_time');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_slots');
    }
};
