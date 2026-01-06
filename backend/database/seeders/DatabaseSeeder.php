<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\TypeReport;
use App\Models\SalesReport;
use App\Models\SalesTarget;
use App\Models\AttendanceSummary;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use App\Models\TypeCustomer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ======================
        // MASTER DATA
        // ======================
        TypeCustomer::insert([
            ['name' => 'BCI'],
            ['name' => 'REG'],
        ]);

        TypeReport::insert([
            ['name' => 'Visit'],          // id = 1
            ['name' => 'Follow Up'],      // id = 2
            ['name' => 'Offering'],       // id = 3
            ['name' => 'Negotiation'],    // id = 4
            ['name' => 'Purchase Order'], // id = 5
        ]);

        // ❗ TANPA ROLE ADMIN
        Role::insert([
            ['name' => "superuser"],              // id = 1
            ['name' => "general manager"],        // id = 2
            ['name' => "manager sales"],          // id = 3
            ['name' => "asistant manager sales"], // id = 4
            ['name' => "branch manager sales"],   // id = 5
            ['name' => "supervisor sales"],       // id = 6
            ['name' => "sales"],                  // id = 7
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

        // ======================
        // USERS
        // ======================

        // sales
        User::factory(17)->create([
            'role_id' => 7,
            'branch_id' => fn () => Branch::inRandomOrder()->first()->id,
        ]);

        // supervisor
        User::factory(6)->create([
            'role_id' => 6,
            'branch_id' => fn () => Branch::inRandomOrder()->first()->id,
        ]);

        // branch manager
        User::factory(2)->create([
            'role_id' => 5,
            'branch_id' => fn () => Branch::inRandomOrder()->first()->id,
        ]);

        // asistant manager
        User::factory(1)->create([
            'role_id' => 4,
            'branch_id' => fn () => Branch::inRandomOrder()->first()->id,
        ]);

        // manager sales
        User::factory(1)->create([
            'role_id' => 3,
            'branch_id' => fn () => Branch::inRandomOrder()->first()->id,
        ]);

        // general manager
        User::factory(1)->create([
            'role_id' => 2,
            'branch_id' => fn () => Branch::inRandomOrder()->first()->id,
        ]);

        // superuser
        User::factory(1)->create([
            'role_id' => 1,
            'branch_id' => fn () => Branch::inRandomOrder()->first()->id,
        ]);

        // ======================
        // HELPER DATA
        // ======================
        $faker = Faker::create();

        $projects = [
            'Light Rail Transit',
            'Rumah Tinggal',
            'Warehouse',
            'Pabrik',
            'Rumah Sakit',
            'Apartemen',
            'Kantor',
            'Jalan Raya',
        ];

        // ambil superuser
        $superUserId = User::where('role_id', 1)->value('id');

        // ambil hanya user sales
        $salesUsers = User::where('role_id', 7)->pluck('id')->toArray();

        if (empty($salesUsers)) {
            throw new Exception('Tidak ada user dengan role SALES');
        }

        // ======================
// SEED SALES REPORT
// ======================
for ($i = 0; $i < 160; $i++) {
    $typeReport = $faker->numberBetween(1, 5);

    SalesReport::create([
        // 🔥 FIX: pastikan ada data 2025 & 2026
        'date' => $faker->dateTimeBetween('2025-01-01', '2026-12-31')->format('Y-m-d'),

        'check_in'  => $faker->time('H:i'),
        'check_out' => $faker->time('H:i'),

        'coordinate_check_in' =>
            $faker->latitude(-6.25, -6.15) . ',' . $faker->longitude(106.75, 106.85),
        'coordinate_check_out' =>
            $faker->latitude(-6.25, -6.15) . ',' . $faker->longitude(106.75, 106.85),

        'user_id' => Arr::random($salesUsers),
        'type_customer_id' => $faker->numberBetween(1, 2),
        'type_report_id'   => $typeReport,

        'customer_name' => $faker->company,
        'type_project'  => $faker->randomElement($projects),
        'project_name'  => $faker->bs,

        'pic_name'     => $faker->name,
        'pic_phone'    => $faker->phoneNumber,
        'pic_position' => $faker->jobTitle,

        'report_notes' => $faker->paragraph(3),
        'equipment_needs' => $faker->words(3, true),

        'items_purchase_order' =>
            $typeReport == 5 ? $faker->words(4, true) : null,

        'nominal_purchase_order' =>
            $typeReport == 5
                ? $faker->numberBetween(5_000_000, 200_000_000)
                : null,

        'is_new_customer' => $faker->boolean(20),
        'picture' => null,

        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// ======================
// SEED ATTENDANCE KPI
// ======================
$years = [2025, 2026];

foreach ($years as $yr) {
    foreach ($salesUsers as $uid) {
        $present = rand(18, 22);
        $working = 22;

        AttendanceSummary::updateOrCreate(
            [
                'user_id' => $uid,
                'month'   => rand(1,12),
                'year'    => $yr,
            ],
            [
                'working_days' => $working,
                'present_days' => $present,
                'absent_days'  => max(0, $working - $present),
                'created_by'   => $superUserId,
            ]
        );
    }
}

// ======================
// SEED SALES TARGET KPI
// ======================
foreach ($years as $yr) {
    foreach ($salesUsers as $uid) {
        SalesTarget::updateOrCreate(
            [
                'user_id' => $uid,
                'month'   => rand(1,12),
                'year'    => $yr,
            ],
            [
                'target_omset' => 150_000_000,
                'target_visit' => 20,
                'target_penawaran' => 8,
                'target_new_customer' => 3,
                'created_by' => $superUserId,
            ]
        );
    }
}

    }
}
