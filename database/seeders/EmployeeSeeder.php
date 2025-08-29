<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            Employee::create([
                'user_id' => null,
                'guid' => Str::uuid()->toString(),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->email(),
                'manager_id' => null,
                'synced_at' => null,
            ]);
        }
    }
}