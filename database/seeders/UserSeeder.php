<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'thoeurn.ratha.kh@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('jfrog123'),
            ]
        );
    }
}