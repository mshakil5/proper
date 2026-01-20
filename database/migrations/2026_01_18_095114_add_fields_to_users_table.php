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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('total_orders')->default(0)->after('postcode');
            $table->dateTime('last_order_date')->nullable()->after('total_orders');
            $table->string('referral_code')->unique()->nullable()->after('last_order_date'); // Generate this
            $table->foreignId('referred_by')->nullable()->constrained('users')->onDelete('set null')->after('referral_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('total_orders');
            $table->dropColumn('last_order_date');
            $table->dropColumn('referral_code');
            $table->dropColumn('referred_by');
        });
    }
};
