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
        Schema::table('contents', function (Blueprint $table) {
            $table->renameColumn('title', 'short_title');
            $table->renameColumn('description', 'short_description');

            $table->string('long_title')->nullable()->after('short_title');
            $table->longText('long_description')->nullable()->after('short_description');
            $table->date('publishing_date')->nullable()->after('long_description');
            $table->string('meta_title')->nullable()->after('type');
            $table->longText('meta_description')->nullable()->after('meta_title');
            $table->longText('meta_keywords')->nullable()->after('meta_description'); // comma-separated
            $table->string('meta_image')->nullable()->after('meta_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->renameColumn('short_title', 'title');
            $table->renameColumn('short_description', 'description');

            $table->dropColumn(['long_title', 'long_description', 'publishing_date', 'meta_title', 'meta_description', 'meta_keywords', 'meta_image']);
        });
    }
};
