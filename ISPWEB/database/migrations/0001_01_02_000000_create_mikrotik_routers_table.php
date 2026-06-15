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
        Schema::create('mikrotik_routers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Router alias/name');
            $table->string('host')->unique()->comment('IP Address or Domain');
            $table->string('username');
            $table->text('password')->comment('Encrypted password');
            $table->integer('api_port')->default(8728);
            $table->enum('status', ['active', 'inactive', 'offline'])->default('active')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_routers');
    }
};
