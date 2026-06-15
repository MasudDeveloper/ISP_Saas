<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null')->after('id');
            $table->decimal('credit_limit', 10, 2)->default(0.00)->after('role');
        });

        // Add 'corporate' to role enum using raw DB statement (to avoid Doctrine DBAL dependency for enum modification)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'isp_staff', 'reseller', 'corporate', 'customer') DEFAULT 'customer'");

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->timestamp('grace_period_used_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'credit_limit']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'isp_staff', 'reseller', 'customer') DEFAULT 'customer'");

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn('grace_period_used_at');
        });
    }
};
