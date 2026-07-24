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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->dateTime('starts_at');
            $table->dateTime('end_at')->nullable();
            $table->dateTime('trial_ends_at')->nullable()->comment('Trial expiration date, if applicable');
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('renews_at')->nullable()->comment('Next scheduled renewal date');
            $table->string('gateway')
                ->nullable()
                ->comment('Payment gateway used for this subscription (e.g. Paystack, Stripe)');

            $table->string('status')->comment('Subscription status (active, trialing, cancelled, expired, pending)');

            $table->json('metadata')
                ->nullable()
                ->comment('Additional subscription metadata from the payment provider');

            $table->index(['user_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
