<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'marketingaeroversumgroup@gmail.com'],
            [
                'name' => 'Admin Beehive',
                'password' => Hash::make('beehivelms123'),
            ]
        );
    }
}
