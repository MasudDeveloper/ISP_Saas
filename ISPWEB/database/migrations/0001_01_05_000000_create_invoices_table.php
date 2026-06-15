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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            
            // Relationships
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->nullable()->constrained('internet_packages')->nullOnDelete();
            
            // Payment details
            $table->decimal('amount', 10, 2);
            $table->text('dynamic_payment_url')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
