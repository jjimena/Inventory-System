<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use HasFactory;

    protected $fillable = [
        'hub_name', // Changed from 'name' to 'hub_name'
        'customer_name',
        'customer_email',
        'phone_number',
        'address',
        'product_id',
        'date',
        // Keep 'description' and 'cost' if they are still relevant
    ];

    // Define relationships here
}
