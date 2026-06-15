<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // === INVENTORY MODULE ===
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_type')->comment('ONU, Router, SFP, Switch');
            $table->string('brand');
            $table->string('serial_number')->unique();
            $table->string('mac_address')->nullable()->unique();
            $table->enum('status', ['in_stock', 'deployed', 'faulty'])->default('in_stock');
            $table->foreignId('assigned_customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('inventory_drums', function (Blueprint $table) {
            $table->id();
            $table->string('drum_number')->unique();
            $table->string('cable_type')->comment('2-Core, 4-Core Drop, etc');
            $table->decimal('total_meters', 10, 2);
            $table->decimal('used_meters', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('inventory_cable_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drum_id')->constrained('inventory_drums')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->decimal('meters_used', 8, 2);
            $table->date('used_date');
            $table->timestamps();
        });

        // === DOUBLE-ENTRY ACCOUNTING MODULE ===
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code')->unique();
            $table->string('account_name');
            $table->enum('account_type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            $table->timestamps();
        });

        Schema::create('accounting_journal', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->date('transaction_date');
            $table->string('reference')->nullable();
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('accounting_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('accounting_journal')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->timestamps();
        });

        // Seed Basic Chart of Accounts
        \Illuminate\Support\Facades\DB::table('chart_of_accounts')->insert([
            ['account_code' => '1000', 'account_name' => 'Cash', 'account_type' => 'Asset'],
            ['account_code' => '4000', 'account_name' => 'Internet Subscription Revenue', 'account_type' => 'Revenue'],
            ['account_code' => '5000', 'account_name' => 'Bandwidth Purchase', 'account_type' => 'Expense'],
            ['account_code' => '5100', 'account_name' => 'Staff Salary', 'account_type' => 'Expense'],
            ['account_code' => '5200', 'account_name' => 'Pole Rent', 'account_type' => 'Expense'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_ledger');
        Schema::dropIfExists('accounting_journal');
        Schema::dropIfExists('chart_of_accounts');
        Schema::dropIfExists('inventory_cable_usage');
        Schema::dropIfExists('inventory_drums');
        Schema::dropIfExists('inventory_items');
    }
};
