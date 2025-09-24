<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use Nette\Utils\Random;
use App\Models\TypeReport;
use App\Models\SalesReport;
use App\Models\TypeProject;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\TypeCustomer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        TypeCustomer::insert([
            ['name' => 'BCI'],
            ['name' => 'REG'],
        ]);

        TypeProject::insert([
            ['name' => 'Gudang'],
            ['name' => 'Gedung'],
            ['name' => 'Jalan'],
            ['name' => 'Power Plant'],
            ['name' => 'Plant'],
            ['name' => 'Data Center'],
            ['name' => 'Oil & Gas'],
            ['name' => 'DAM'],
        ]);

        TypeReport::insert([
            ['name' => 'Visit'],
            ['name' => 'Follow Up'],
            ['name' => 'Offering'],
            ['name' => 'Negotiation'],
            ['name' => 'Purchase Order'],
        ]);

        Role::insert([
            ['name' => "superuser"],
            ['name' => "admin"],
            ['name' => "general manager"],
            ['name' => "manager sales"],
            ['name' => "asistant manager sales"],
            ['name' => "branch manager sales"],
            ['name' => "supervisor sales"],
            ['name' => "sales"],
        ]);

        Branch::insert([
            ['name' => "Jakarta 1"],
            ['name' => "Jakarta 2"],
            ['name' => "Jakarta 3"],
            ['name' => "Bali"],
            ['name' => "Balikpapan"],
            ['name' => "Semarang"],
            ['name' => "Surabaya"],
            ['name' => "Medan"],
            ['name' => "Makassar"],
        ]);

        User::factory(17)->create([
            'role_id' => 8,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);

        User::factory(6)->create([
            'role_id' => 6,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);

        User::factory(2)->create([
            'role_id' => 5,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);
        User::factory(1)->create([
            'role_id' => 7,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);
        User::factory(1)->create([
            'role_id' => 4,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);
        User::factory(1)->create([
            'role_id' => 3,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);
        User::factory(1)->create([
            'role_id' => 2,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);
        User::factory(1)->create([
            'role_id' => 1,
            'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        ]);

        $faker = Faker::create();
        for ($i = 0; $i < 60; $i++) {

            SalesReport::create([
                'date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                'check_in' => $faker->time('H:i:s'),
                'check_out' => $faker->time('H:i:s'),
                'coordinate_check_in' => $faker->latitude(-6.25, -6.15) . ',' . $faker->longitude(106.75, 106.85),
                'coordinate_check_out' => $faker->latitude(-6.25, -6.15) . ',' . $faker->longitude(106.75, 106.85),
                'user_id' => $faker->numberBetween(1, 17),
                'type_customer_id' => $faker->numberBetween(1, 2),
                'type_project_id' => $faker->numberBetween(1, 8),
                'type_report_id' => $faker->numberBetween(1, 5),
                'customer_name' => $faker->company,
                'project_name' => $faker->bs,
                'pic_name' => $faker->name,
                'pic_phone' => $faker->phoneNumber,
                'pic_position' => $faker->jobTitle,
                'report_notes' => $faker->paragraph(3),
                'equipment_needs' => $faker->words(3, true),
                'items_purchase_order' => $faker->words(4, true),
                'nominal_purchase_order' => $faker->numberBetween(5_000_000, 200_000_000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        };
    }
}
