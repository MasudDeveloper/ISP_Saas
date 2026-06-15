<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResellerController extends Controller
{
    /**
     * Create a Child Reseller or Corporate Account
     */
    public function createSubAccount(Request $request)
    {
        $parent = $request->user();

        // Only allow Super Admin or Reseller to create sub-accounts
        if (!in_array($parent->role, ['super_admin', 'reseller'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:reseller,corporate,customer',
            'credit_limit' => 'nullable|numeric|min:0'
        ]);

        // A Reseller can't give more credit than they have (unless they are super_admin)
        $creditToAssign = $request->credit_limit ?? 0;
        
        if ($parent->role === 'reseller') {
            if ($parent->credit_limit < $creditToAssign) {
                return response()->json(['error' => 'Insufficient credit limit'], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Deduct credit from parent if applicable
            if ($parent->role === 'reseller' && $creditToAssign > 0) {
                $parent->decrement('credit_limit', $creditToAssign);
            }

            // Create Sub-Account
            $child = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'parent_id' => $parent->id,
                'credit_limit' => $creditToAssign
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sub-account created successfully',
                'data' => $child
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create sub-account'], 500);
        }
    }
}
