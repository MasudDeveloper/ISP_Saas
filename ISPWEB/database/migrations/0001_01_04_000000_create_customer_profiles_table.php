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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('router_id')->constrained('mikrotik_routers')->onDelete('restrict');
            $table->foreignId('package_id')->constrained('internet_packages')->onDelete('restrict');
            
            // Mikrotik info
            $table->ipAddress('ip_address')->nullable();
            $table->string('pppoe_username')->unique();
            $table->string('pppoe_password');
            $table->timestamp('expiry_date')->nullable()->index();
            $table->enum('status', ['Active', 'Expired', 'Disabled'])->default('Disabled')->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
