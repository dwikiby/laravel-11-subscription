<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic',
            'description' => 'The Basic plan is perfect for individuals or small teams just getting started. It includes essential features to help you manage your projects effectively, such as task management, basic reporting, and up to 5 user accounts.',
            'price' => 50000,
            'duration' => 1
        ]);

        Plan::create([
            'name' => 'Standard',
            'description' => 'The Standard plan offers a great balance of features and affordability. Ideal for growing teams, it includes all the features of the Basic plan plus additional reporting tools, integrations with third-party apps, and up to 20 user accounts.',
            'price' => 100000,
            'duration' => 1
        ]);

        Plan::create([
            'name' => 'Premium',
            'description' => 'The Premium plan is designed for large teams or organizations that require advanced features and customization. It includes everything in the Standard plan plus premium support, advanced analytics, unlimited user accounts, and custom workflows.',
            'price' => 300000,
            'duration' => 1
        ]);
    }
}
