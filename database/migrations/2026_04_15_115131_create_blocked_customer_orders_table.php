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
        Schema::create('blocked_customer_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blocked_customer_id');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('order_data');
            $table->timestamps();
            
            $table->foreign('blocked_customer_id')->references('id')->on('blocked_customers')->onDelete('cascade');
            $table->index('blocked_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_customer_orders');
    }
};
