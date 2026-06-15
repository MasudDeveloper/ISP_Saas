<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('btrc_logs', function (Blueprint $table) {
            $table->id();
            $table->string('username')->index();
            $table->ipAddress('src_ip')->index();
            $table->ipAddress('dst_ip')->nullable();
            $table->macAddress('mac_address')->nullable();
            $table->integer('src_port')->nullable();
            $table->integer('dst_port')->nullable();
            $table->string('protocol')->nullable();
            $table->timestamp('session_start')->nullable();
            $table->timestamp('session_end')->nullable();
            $table->bigInteger('bytes_up')->default(0);
            $table->bigInteger('bytes_down')->default(0);
            
            // Partitioning/Indexing optimization for massive data
            $table->index(['session_start', 'session_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('btrc_logs');
    }
};
