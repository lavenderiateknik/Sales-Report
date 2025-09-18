<?php

namespace Database\Seeders;

use App\Models\TypeCustomer;
use App\Models\TypeProject;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        TypeCustomer::insert([
            ['name_type_customer' => 'BCI'],
            ['name_type_customer' => 'REG'],
        ]);
        TypeProject::insert([
            ['name_type_project' => 'Gudang'],
            ['name_type_project' => 'Gedung'],
            ['name_type_project' => 'Jalan'],
            ['name_type_project' => 'Power Plant'],
            ['name_type_project' => 'Plant'],
            ['name_type_project' => 'Data Center'],
            ['name_type_project' => 'Oil & Gas'],
            ['name_type_project' => 'DAM'],
        ]);
    }
}
