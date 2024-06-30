<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class Role extends Model
{
    use HasFactory;

    const ADMIN = 1;
    const STAFF = 2;
    const HUB = 3;
    // const USER = 1;

    protected $table = "roles";
    protected $primaryKey = "id";
    public $timestamps = false;
    public $increments = true;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role'); // Assuming you have a user_role pivot table
    }

    public static function getRoleNameById($roleId)
    {
        return Role::find($roleId)->name;
    }

    // Method to check if the authenticated user is an admin
    public static function isAdmin()
    {
        // Assuming you have a way to access the authenticated user's role,
        // here we're using Laravel's Auth facade to get the authenticated user
        // and then checking if their role matches the ADMIN constant.
        // Replace this logic with your actual method of determining the authenticated user's role.
        $authenticatedUserRole = Auth::user()->role_id?? null;

        return $authenticatedUserRole === self::ADMIN;
    }

}



    // public static function getRoleName($roleId)
    // {
    //     switch ($roleId) {
    //         case self::ADMIN:
    //             return 'Admin';
    //         case self::USER:
    //             default:
    //             return 'User';
    //     }
    // }
    
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'name');
    // }