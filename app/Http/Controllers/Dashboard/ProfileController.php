<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $roleName = Role::getRoleNameById($user->role_id);
        
        return view('dashboard.profile.show', compact('user', 'roleName'));
    }
}
