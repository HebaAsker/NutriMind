<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ChatSeeder;
use Database\Seeders\DoctorSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\PatientSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DoctorSeeder::class,
            PatientSeeder::class,
            ChatSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
