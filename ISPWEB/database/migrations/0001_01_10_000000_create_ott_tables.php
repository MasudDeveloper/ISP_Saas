<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add JSON column to packages
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->json('included_otts')->nullable()->after('mikrotik_profile_name')->comment('Array of providers e.g. ["chorki", "hoichoi"]');
        });

        // Create Subscriptions Table
        Schema::create('ott_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('provider')->comment('chorki, hoichoi, bongo');
            $table->string('phone_number')->comment('Number used for activation');
            $table->enum('status', ['active', 'expired', 'failed'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ott_subscriptions');
        
        Schema::table('internet_packages', function (Blueprint $table) {
            $table->dropColumn('included_otts');
        });
    }
};
