<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = "orders";
    protected $primaryKey = "id";
    public $timestamps = true;
    public $increments = true;

    protected $fillable = ['date', 'total_price', 'customer_id', 'total_quantity'];
    // protected $fillable = ['date', 'total_price', 'customer_name', 'customer_email', 'user_id',];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    // public function orderItems()
    // {
    //     return $this->hasMany(OrderItem::class);
    // }
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }// In your Order model
    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

}
