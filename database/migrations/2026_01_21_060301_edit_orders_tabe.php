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
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('other_discount', 'gift_card_discount');
            $table->foreignId('gift_card_id')->nullable()->after('coupon_id')->constrained('gift_cards')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('gift_card_discount', 'other_discount');
            $table->dropForeignKeyIfExists(['gift_card_id']);
            $table->dropColumn('gift_card_id');
        });
    }
};
