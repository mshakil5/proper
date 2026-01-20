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
        Schema::create('delivery_subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_subscription_id')
                  ->constrained('delivery_subscriptions')
                  ->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['paid', 'failed', 'pending'])->default('pending');
            $table->date('billing_month');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_ref')->nullable();
            $table->timestamps();
            
            $table->index('delivery_subscription_id');
            $table->index('status');
            $table->index('billing_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_subscription_payments');
    }
};
