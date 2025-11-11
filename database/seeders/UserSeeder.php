<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $users = [];
        $password = Hash::make('password123'); // A common, secure password for seeders

        // --- 1. Admin Accounts (usertype = 'admin') ---
        $users[] = [
            'name' => 'ByondAdmin1',
            'email' => 'byondadmin1@example.com',
            'usertype' => 'admin',
            'password' => $password,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $users[] = [
            'name' => 'ByondAdmin2',
            'email' => 'byondadmin2@example.com',
            'usertype' => 'admin',
            'password' => $password,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // --- 2. Tester Accounts (usertype = 'user') ---
        // Loop to create 5 tester accounts
        for ($i = 1; $i <= 5; $i++) {
            $users[] = [
                'name' => "Tester User $i",
                'email' => "tester$i@example.com",
                'usertype' => 'user', // Default client/tester type
                'password' => $password,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert all defined users into the 'users' table
        DB::table('users')->insert($users);
    }
}