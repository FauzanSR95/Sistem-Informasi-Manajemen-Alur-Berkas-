<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat user Admin utama secara otomatis
        User::updateOrCreate(
            ['email' => 'admin@bpn.go.id'],
            [
                'name' => 'Admin BPN',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Memanggil seeder untuk pengaturan
        $this->call(SettingSeeder::class);
    }
}