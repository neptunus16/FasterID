<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super Admin ──────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@fasterid.com'],
            [
                'username' => 'admin',
                'name'     => 'Admin Faster ID',
                'email'    => 'admin@fasterid.com',
                'phone'    => '081200000001',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        // ── Sample Customer ──────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'customer@fasterid.com'],
            [
                'username' => 'budi_customer',
                'name'     => 'Budi Santoso',
                'email'    => 'customer@fasterid.com',
                'phone'    => '081200000002',
                'password' => Hash::make('customer123'),
                'role'     => 'customer',
                'status'   => 'active',
            ]
        );

        // ── Sample Driver (active) ────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'driver@fasterid.com'],
            [
                'username' => 'andi_driver',
                'name'     => 'Andi Kurniawan',
                'email'    => 'driver@fasterid.com',
                'phone'    => '081200000003',
                'password' => Hash::make('driver123'),
                'role'     => 'driver',
                'status'   => 'active',
                'is_online' => 1,
            ]
        );

        // ── Sample Driver (pending) ────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'driver2@fasterid.com'],
            [
                'username' => 'sari_driver',
                'name'     => 'Sari Indah',
                'email'    => 'driver2@fasterid.com',
                'phone'    => '081200000004',
                'password' => Hash::make('driver123'),
                'role'     => 'driver',
                'status'   => 'pending',
            ]
        );

        $this->command->info('✅ Admin, Customer, dan Driver sample berhasil dibuat.');
    }
}
