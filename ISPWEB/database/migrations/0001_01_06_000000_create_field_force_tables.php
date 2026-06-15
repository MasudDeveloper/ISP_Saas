<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tj_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('total_ports')->default(8);
            $table->integer('used_ports')->default(0);
            $table->timestamps();
        });

        Schema::create('fiber_lines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('coordinates')->comment('Array of lat,lng objects for polyline');
            $table->string('color')->default('#3B82F6');
            $table->timestamps();
        });

        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('current_lat', 10, 8)->nullable();
            $table->decimal('current_lng', 11, 8)->nullable();
            $table->boolean('is_free')->default(true);
            $table->string('fcm_token')->nullable()->comment('Firebase Push Token');
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('category');
            $table->text('description')->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->foreignId('assigned_technician_id')->nullable()->constrained('technicians')->onDelete('set null');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->timestamps();
        });

        // Add coordinates to customer profiles
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();
            $table->foreignId('tj_box_id')->nullable()->constrained('tj_boxes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropForeign(['tj_box_id']);
            $table->dropColumn(['lat', 'lng', 'tj_box_id']);
        });
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('technicians');
        Schema::dropIfExists('fiber_lines');
        Schema::dropIfExists('tj_boxes');
    }
};
