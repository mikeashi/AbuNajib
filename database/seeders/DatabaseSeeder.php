<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@abunajib.com',
            'password' => Hash::make("pass"),
        ]);

        
        $seeders = [
            AccountTypeSeeder::class,
            AccountSeeder::class,
            TransactionGroupSeeder::class,
            TransactionCategorySeeder::class,
        ];

        collect($seeders)->each(function ($seeder) {
            if (class_exists($seeder)) {
                $this->call($seeder);
            }
        });
    }
}
