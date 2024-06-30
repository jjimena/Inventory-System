<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

// Check if the admin role exists
// if (!Role::where('name', 'admin')->exists()) {
//     // Create the admin role if it doesn't exist
//     Role::create(['name' => 'admin']);
// }

class User extends Authenticatable
{
    
    
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "users";
    protected $primaryKey = "id";
    public $timestamps = true;
    public $increments = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone_number', // Added phone_number field
        'hub_name', // Added hub_name field
        'address', // Added location field
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'id');
    }
    
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'user_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }
    // In User model
    // public function phoneNumber()
    // {
    //     return $this->hasOne(PhoneNumber::class);
    // }

    // public function hub()
    // {
    //     return $this->belongsTo(Hub::class);
    // }





    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'user_role'); // Assuming you have a user_role pivot table
    // }
    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class, 'name');
    // }

    // public function users(): HasMany
    // {
    //     return $this->hasMany(User::class, 'name', 'id'); // Assuming 'id' is the shared primary key
    // }

    // public function getRoleAttribute()
    // {
    //     return Role::getRoleName($this->role);
    // }
}
