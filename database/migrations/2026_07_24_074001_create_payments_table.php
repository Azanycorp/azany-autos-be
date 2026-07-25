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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->nullableMorphs('payable');
            // payable_type: Related model (Subscription, Wallet, Booking, etc.)
            // payable_id: Related model ID

            $table->string('type')->comment('Payment purpose (subscription, wallet_topup, etc.)');
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable()->unique();
            $table->string('channel')
                ->nullable()
                ->comment('Payment channel (card, bank_transfer, ussd, qr, etc.)');

            $table->string('currency')->default('NGN');

            $table->string('gateway')
                ->nullable()
                ->comment('Payment gateway used (Paystack, Flutterwave, Stripe, etc.)');

            $table->string('ip_address')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->string('status')->comment('Payment status (pending, success, failed, refunded, abandoned)');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('metadata')->nullable();

            $table->index(['user_id', 'type', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
