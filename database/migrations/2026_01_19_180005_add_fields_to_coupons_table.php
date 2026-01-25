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
        Schema::table('coupons', function (Blueprint $table) {
            $table->enum('coupon_type', ['coupon', 'voucher'])->default('coupon')->after('is_active');
            $table->boolean('is_birthday_voucher')->default(false)->after('coupon_type');
            $table->integer('max_uses_per_user')->nullable()->after('max_uses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('coupon_type');
            $table->dropColumn('is_birthday_voucher');
            $table->dropColumn('max_uses_per_user');
        });
    }
};
