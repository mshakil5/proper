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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('amount', 8, 2);
            $table->decimal('balance', 8, 2);
            $table->enum('status', ['new', 'used', 'expired'])->default('new');
            $table->boolean('is_active')->default(true);
            $table->foreignId('purchased_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->timestamp('purchased_at')->nullable();
            $table->string('sent_to_email')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index('code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
