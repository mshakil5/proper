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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('has_attribute')->default(0)->after('price');
            $table->string('attribute_name')->nullable()->after('has_attribute');
            $table->decimal('attribute_price', 8, 2)->default(0)->after('attribute_name');
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('has_attribute');
            $table->dropColumn('attribute_name');
            $table->dropColumn('attribute_price');
            $table->dropColumn('stock_status');
        });
    }
};
