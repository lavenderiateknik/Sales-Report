<?php

use Illuminate\Database\Seeder;
use App\Models\SalesReport;
use App\Models\TypeCustomer;
use App\Models\TypeProject;
use App\Models\TypeReport;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

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

        SalesReport::insert([
            [
                'date' => now()->toDateString(),
                'check_in' => '09:00:00',
                'coordinate_check_in' => '-6.2000000,106.8166667',
                'type_customer_id' => 1,
                'customer_name' => 'PT Maju Jaya',
                'type_project_id' => 2,
                'project_name' => 'Data Center Upgrade',
                'pic_name' => 'Budi Santoso',
                'pic_phone' => '08123456789',
                'pic_position' => 'IT Manager',
                'type_report_id' => 1,
                'report_notes' => 'Diskusi panjang tentang kebutuhan sistem, upgrade server, dan alur pembelian.', // 👈 tambahan
                'equipment_needs' => 'Server Rack, UPS',
                'items_purchase_order' => 'Dell PowerEdge, APC UPS',
                'nominal_purchase_order' => 150000000.00,
                'check_out' => '11:30:00',
                'coordinate_check_out' => '-6.2010000,106.8170000',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
