<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Contoh Pengguna
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@daskin.test',
            'password' => Hash::make('password'),
        ])->assignRole('superadmin');

        // Pengguna ini adalah seorang Editor di departemen Itjen
        User::create([
            'name' => 'Editor Itjen',
            'email' => 'editor.itjen@daskin.test',
            'password' => Hash::make('password'),
        ])->assignRole(['itjen', 'editor']);

        // Pengguna ini adalah seorang Viewer di departemen Sekjen
        User::create([
            'name' => 'Viewer Sekjen',
            'email' => 'viewer.sekjen@daskin.test',
            'password' => Hash::make('password'),
        ])->assignRole(['sekjen', 'viewer']);
        
        // Pengguna ini hanya seorang Menteri (viewer)
        User::create([
            'name' => 'Menteri',
            'email' => 'menteri@daskin.test',
            'password' => Hash::make('password'),
        ])->assignRole(['menteri', 'viewer']);
    }
}