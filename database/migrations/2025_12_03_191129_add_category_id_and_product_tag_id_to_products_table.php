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
            $table->decimal('price', 10, 2)->nullable()->after('title');
            $table->unsignedBigInteger('category_id')->nullable()->after('price');
            $table->unsignedBigInteger('product_tag_id')->nullable()->after('category_id');
            
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('product_tag_id')->references('id')->on('product_tags')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropForeign(['category_id']);
            $table->dropForeign(['product_tag_id']);
            $table->dropColumn(['category_id', 'product_tag_id']);
        });
    }
};
