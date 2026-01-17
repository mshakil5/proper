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
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            
            // Profile
            $table->string('image')->default('/placeholder.webp')->after('last_name');
            $table->date('dob')->nullable()->after('image');
            
            // Company/Business
            $table->string('company_name')->nullable()->after('dob');
            
            // Address
            $table->text('address_1')->nullable()->after('company_name');
            $table->text('address_2')->nullable()->after('address_1');
            $table->string('street')->nullable()->after('address_2');
            $table->string('city')->nullable()->after('street');
            $table->string('state')->nullable()->after('city');
            $table->string('postcode')->nullable()->after('state');
            
            // Marketing
            $table->boolean('sms_marketing')->default(0)->after('password');
            $table->boolean('email_marketing')->default(0)->after('sms_marketing');
            $table->boolean('newsletter')->default(0)->after('email_marketing');
            $table->timestamp('last_login')->nullable()->after('newsletter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('image');
            $table->dropColumn('dob');
            $table->dropColumn('company_name');
            $table->dropColumn('address_1');
            $table->dropColumn('address_2');
            $table->dropColumn('street');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('postcode');
            $table->dropColumn('sms_marketing');
            $table->dropColumn('email_marketing');
            $table->dropColumn('newsletter');
            $table->dropColumn('last_login');
        });
    }
};
