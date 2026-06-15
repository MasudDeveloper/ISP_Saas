<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'router_id',
        'package_id',
        'ip_address',
        'pppoe_username',
        'pppoe_password',
        'expiry_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the router associated with this profile.
     */
    public function router()
    {
        return $this->belongsTo(MikrotikRouter::class, 'router_id');
    }

    /**
     * Get the package associated with this profile.
     */
    public function package()
    {
        return $this->belongsTo(InternetPackage::class, 'package_id');
    }
}
