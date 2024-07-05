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

    protected $table = "roles";
    protected $primaryKey = "id";
    public $timestamps = false;
    public $increments = true;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    public static function getRoleNameById($roleId)
    {
        return Role::find($roleId)->name;
    }

    public static function isAdmin()
    {
        $authenticatedUserRole = Auth::user()->role_id?? null;

        return $authenticatedUserRole === self::ADMIN;
    }
}