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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('order_number')->unique();
            $table->string('customer_type')->nullable();
            
            // Customer info
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Address
            $table->text('address_1')->nullable();
            $table->text('address_2')->nullable();
            $table->string('street')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            
            // Delivery
            $table->enum('delivery_type', ['delivery', 'collection']);
            $table->string('time')->nullable();
            
            // Pricing
            $table->decimal('subtotal', 10, 2);
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->decimal('points_used', 10, 2)->default(0);
            $table->decimal('other_discount', 10, 2)->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->decimal('total', 10, 2);
            
            // Payment
            $table->enum('payment_method', ['cash', 'stripe', 'paypal']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_transaction_id')->nullable()->unique();
            
            // Status & HubRise
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('hubrise_order_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
