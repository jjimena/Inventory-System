<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'Admin')->first();

        # role admin
        User::create([
            'name' => 'test1',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'role' => 1,
        ]);

        # role staff
        User::create([
            'name' => 'test2',
            'email' => 'test2@gmail.com',
            'password' => 'password',
            'role' => 2,
        ]);
        # role hub
        User::create([
            'name' => 'test3',
            'email' => 'test3@gmail.com',
            'password' => 'password',
            'role' => 3,
        ]);
    }
}
