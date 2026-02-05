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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_type'); // order, giftcard, subscription, etc
            $table->unsignedBigInteger('reference_id'); // order_id, giftcard_id, subscription_id
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('GBP');
            $table->string('payment_method'); // stripe, paypal, cash
            $table->string('transaction_id')->nullable(); // stripe_id, paypal_id
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->json('metadata')->nullable(); // store extra info
            $table->timestamps();

            // optional index for faster lookups
            $table->index(['payment_type', 'reference_id']);
            $table->index('transaction_id');
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
