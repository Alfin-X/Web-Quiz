<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample guru users
        User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru.math@iqes.com',
            'role' => 'guru',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru Bahasa Indonesia',
            'email' => 'guru.bahasa@iqes.com',
            'role' => 'guru',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru IPA',
            'email' => 'guru.ipa@iqes.com',
            'role' => 'guru',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Guru Sejarah',
            'email' => 'guru.sejarah@iqes.com',
            'role' => 'guru',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
