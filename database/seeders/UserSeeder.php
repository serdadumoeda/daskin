<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus pengguna lama jika ada (opsional, hati-hati di produksi)
        // User::truncate();

        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SUPERADMIN,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin Itjen',
            'email' => 'itjen@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ITJEN,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin Sekjen',
            'email' => 'sekjen@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_SEKJEN,
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'Admin Binapenta',
            'email' => 'binapenta@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_BINAPENTA,
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'Admin Binalavotas',
            'email' => 'binalavotas@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_BINALAVOTAS,
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'Admin Binwasnaker',
            'email' => 'binwasnaker@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_BINWASNAKER,
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'Admin PHI',
            'email' => 'phi@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PHI,
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'Admin Barenbang',
            'email' => 'barenbang@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_BARENBANG,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pengguna Biasa', // User default
            'email' => 'user@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER, 
            'email_verified_at' => now(),
        ]);

        // --- TAMBAHKAN SEEDER UNTUK ROLE BARU READ-ONLY ---
        User::create([
            'name' => 'Menteri',
            'email' => 'menteri@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MENTERI,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Wakil Menteri',
            'email' => 'wamen@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_WAKIL_MENTERI,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Staff Khusus Menteri',
            'email' => 'staffsus.menteri@kemnaker.go.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STAFF_KHUSUS,
            'email_verified_at' => now(),
        ]);
        // --------------------------------------------------
    }
}