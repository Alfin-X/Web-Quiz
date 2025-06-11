<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@quiz.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::create([
            'name' => 'User Test',
            'email' => 'user@quiz.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create categories
        Category::create([
            'name' => 'Matematika',
            'description' => 'Kuis tentang matematika dasar dan lanjutan',
        ]);

        Category::create([
            'name' => 'Bahasa Indonesia',
            'description' => 'Kuis tentang tata bahasa dan sastra Indonesia',
        ]);

        Category::create([
            'name' => 'Sejarah',
            'description' => 'Kuis tentang sejarah Indonesia dan dunia',
        ]);

        Category::create([
            'name' => 'Sains',
            'description' => 'Kuis tentang ilmu pengetahuan alam',
        ]);
    }
}
