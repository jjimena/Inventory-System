<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone_number',
        'hub_name',
        'address',
        'date'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class); // Assuming an Order belongs to a Customer
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
