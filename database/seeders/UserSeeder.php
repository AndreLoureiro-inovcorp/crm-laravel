<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'tenant_id' => 1,
            'name' => 'Andre',
            'email' => 'teste@gmail.com',
            'password' => Hash::make('andreteste'),
            'email_verified_at' => now(),
        ]);
    }
}
