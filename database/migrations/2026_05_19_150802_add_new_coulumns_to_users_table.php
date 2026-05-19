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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->string('status')->nullable();
            $table->string('user_type')->nullable();
            $table->string('phone')->nullable();
            $table->string('reg_number')->nullable();
            $table->string('business_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('verification_code')->nullable();
            $table->dateTime('verification_code_expire_at')->nullable();
            $table->longText('profile_photo')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->boolean('kyc_verification')->default(false);
            $table->boolean('biometric_enabled')->default(false);
            $table->boolean('lock_screen_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'country_id',
                'status',
                'user_type',
                'phone',
                'reg_number',
                'business_name',
                'contact_person',
                'verification_code',
                'verification_code_expire_at',
                'profile_photo',
                'state',
                'city',
                'address',
                'zip_code',
                'two_factor_enabled',
                'kyc_verification',
                'biometric_enabled',
                'lock_screen_enabled'
            ]);
        });
    }
};
