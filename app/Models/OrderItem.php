<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';

    protected $table = "order_items";
    protected $primaryKey = "id";
    public $timestamps = true;
    public $increments = true;

    
    protected $fillable = ['quantity', 'unit_price', 'product_id', 'customer_id', 'order_id', 'status', 'payment_method', 'reference_number' ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }
}
