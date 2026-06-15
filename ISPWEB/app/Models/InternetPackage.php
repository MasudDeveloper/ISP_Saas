<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternetPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'speed_mbps',
        'price',
        'mikrotik_profile_name',
    ];

    /**
     * Get the customer profiles associated with this package.
     */
    public function customerProfiles()
    {
        return $this->hasMany(CustomerProfile::class, 'package_id');
    }

    /**
     * Get the invoices associated with this package.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'package_id');
    }
}
