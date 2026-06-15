<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Check if admin exists
        $exists = DB::table('users')->where('email', 'admin@mrdeveloper.com')->exists();
        
        if (!$exists) {
            DB::table('users')->insert([
                'name' => 'Super Admin',
                'email' => 'admin@mrdeveloper.com',
                'password' => Hash::make('12345678'),
                'role' => 'super_admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'admin@mrdeveloper.com')->delete();
    }
};
