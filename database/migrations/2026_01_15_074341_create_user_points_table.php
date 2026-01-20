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
        Schema::create('user_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('referrer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('source')->default('order'); // order, referral, social_share, giftcard_balance
            $table->string('source_action')->nullable(); // facebook_share, twitter_share, instagram_share, etc
            $table->integer('point')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_points');
    }
};
