<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MikrotikRouter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'host',
        'username',
        'password',
        'api_port',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the customer profiles associated with this router.
     */
    public function customerProfiles()
    {
        return $this->hasMany(CustomerProfile::class, 'router_id');
    }
}
