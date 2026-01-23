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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ======================
           MASTER DATA
        ====================== */
        TypeCustomer::insert([
            ['name' => 'BCI'],
            ['name' => 'REG'],
        ]);

        TypeReport::insert([
            ['name' => 'Visit'],
            ['name' => 'Follow Up'],
            ['name' => 'Offering'],
            ['name' => 'Negotiation'],
            ['name' => 'Purchase Order'],
            ['name' => 'Phone Call'],
        ]);

        // ❗ TANPA ROLE ADMIN
        Role::insert([
            ['name' => "superuser"],              // 1
            ['name' => "general manager"],        // 2
            ['name' => "manager sales"],          // 3
            ['name' => "asistant manager sales"], // 4
            ['name' => "branch manager sales"],   // 5
            ['name' => "supervisor sales"],       // 6
            ['name' => "sales"],                  // 7
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

        $path = database_path('seeders/data/users.csv');

        if (!File::exists($path)) {
            $this->command->error('File users.csv tidak ditemukan');
            return;
        }

        $file = fopen($path, 'r');

        // ⚠️ delimiter SEMICOLON
        $rawHeader = fgetcsv($file, 0, ';');

        $header = array_map(fn ($h) => strtolower(trim($h)), $rawHeader);

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            $data = array_combine($header, $row);

            // skip jika email kosong
            if (empty($data['email'])) {
                continue;
            }

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make($data['password'] ?? 'passwordawal'),
                    'role_id'           => $data['role_id'],
                    'branch_id'         => $data['branch_id'] ?: null,
                    'email_verified_at' => $data['email_verified_at'] ?: null,
                ]
            );
        }

        fclose($file);

        $this->command->info('Seeder User berhasil dijalankan.');
    }

        /* ======================
           USERS
        ====================== */
        // User::factory(17)->create(['role_id' => 7, 'branch_id' => fn()=>Branch::inRandomOrder()->first()->id]);
        // User::factory(6)->create(['role_id' => 6, 'branch_id' => fn()=>Branch::inRandomOrder()->first()->id]);
        // User::factory(2)->create(['role_id' => 5, 'branch_id' => fn()=>Branch::inRandomOrder()->first()->id]);
        // User::factory(1)->create(['role_id' => 4, 'branch_id' => fn()=>Branch::inRandomOrder()->first()->id]);
        // User::factory(1)->create(['role_id' => 3, 'branch_id' => fn()=>Branch::inRandomOrder()->first()->id]);
        // User::factory(1)->create(['role_id' => 2, 'branch_id' => fn()=>Branch::inRandomOrder()->first()->id]);
        // User::factory(1)->create(['role_id' => 1, 'branch_id' => fn()=>Branch::inRandomOrder()->first()->id]);

        /* ======================
           HELPER
        ====================== */
        // $faker = Faker::create();

        // $projects = [
        //     'Light Rail Transit','Rumah Tinggal','Warehouse','Pabrik',
        //     'Rumah Sakit','Apartemen','Kantor','Jalan Raya',
        // ];

        // $superUserId = User::where('role_id', 1)->value('id');
        // $salesUsers  = User::where('role_id', 7)->pluck('id')->toArray();

        // if (empty($salesUsers)) {
        //     throw new Exception('Tidak ada user dengan role SALES');
        // }

        /* ======================
           SALES REPORT (2 TAHUN)
        ====================== */
        // for ($i = 0; $i < 200; $i++) {
        //     $typeReport = $faker->numberBetween(1, 5);

        //     SalesReport::create([
        //         'date' => $faker->dateTimeBetween('2025-01-01', '2026-12-31')->format('Y-m-d'),
        //         'check_in'  => $faker->time('H:i'),
        //         'check_out' => $faker->time('H:i'),

        //         'coordinate_check_in' =>
        //             $faker->latitude(-6.25, -6.15) . ',' . $faker->longitude(106.75, 106.85),
        //         'coordinate_check_out' =>
        //             $faker->latitude(-6.25, -6.15) . ',' . $faker->longitude(106.75, 106.85),

        //         'user_id' => Arr::random($salesUsers),
        //         'type_customer_id' => $faker->numberBetween(1, 2),
        //         'type_report_id'   => $typeReport,

        //         'customer_name' => $faker->company,
        //         'type_project'  => $faker->randomElement($projects),
        //         'project_name'  => $faker->bs,

        //         'pic_name'     => $faker->name,
        //         'pic_phone'    => $faker->phoneNumber,
        //         'pic_position' => $faker->jobTitle,

        //         'report_notes' => $faker->paragraph(2),
        //         'equipment_needs' => $faker->words(3, true),

        //         'items_purchase_order' =>
        //             $typeReport == 5 ? $faker->words(4, true) : null,

        //         'nominal_purchase_order' =>
        //             $typeReport == 5
        //                 ? $faker->numberBetween(5_000_000, 200_000_000)
        //                 : null,

        //         'is_new_customer' => $faker->boolean(20),
        //         'picture' => null,
        //     ]);
        // }

        /* ======================
           ATTENDANCE KPI (12 BULAN)
        ====================== */
        // $years = [2025, 2026];

        // foreach ($years as $yr) {
        //     foreach ($salesUsers as $uid) {
        //         for ($m = 1; $m <= 12; $m++) {

        //             $working = 22;
        //             $present = rand(15, 22);

        //             AttendanceSummary::updateOrCreate(
        //                 [
        //                     'user_id' => $uid,
        //                     'month'   => $m,
        //                     'year'    => $yr,
        //                 ],
        //                 [
        //                     'working_days' => $working,
        //                     'present_days' => $present,
        //                     'absent_days'  => max(0, $working - $present),
        //                     'created_by'   => $superUserId,
        //                 ]
        //             );
        //         }
        //     }
        // }

        /* ======================
           SALES TARGET KPI (12 BULAN)
        ====================== */
        // foreach ($years as $yr) {
        //     foreach ($salesUsers as $uid) {
        //         for ($m = 1; $m <= 12; $m++) {

        //             SalesTarget::updateOrCreate(
        //                 [
        //                     'user_id' => $uid,
        //                     'month'   => $m,
        //                     'year'    => $yr,
        //                 ],
        //                 [
        //                     'target_omset'        => rand(120_000_000, 200_000_000),
        //                     'target_visit'       => rand(15, 25),
        //                     'target_penawaran'   => rand(5, 10),
        //                     'target_new_customer'=> rand(2, 5),
        //                     'created_by'         => $superUserId,
        //                 ]
        //             );
        //         }
        //     }
        // }
    
}
