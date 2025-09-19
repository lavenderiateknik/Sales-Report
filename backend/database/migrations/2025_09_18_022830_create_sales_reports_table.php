<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date');

            // check-in
            $table->time('check_in');
            $table->string('coordinate_check_in')->nullable(); // lat,lng gabungan

            // relasi ke type_customers
            $table->foreignId('type_customer_id')->constrained('type_customers')->onDelete('cascade');
            $table->string('customer_name');

            // relasi ke type_projects
            $table->foreignId('type_project_id')->constrained('type_projects')->onDelete('cascade');
            $table->string('project_name');

            // PIC (person in charge)
            $table->string('pic_name');
            $table->string('pic_phone');
            $table->string('pic_position');

            // relasi ke type_reports
            $table->foreignId('type_report_id')->constrained('type_reports')->onDelete('cascade');

            // tambahan notes panjang
            $table->longText('report_notes')->nullable();

            $table->text('equipment_needs')->nullable();
            $table->text('items_purchase_order')->nullable();
            $table->decimal('nominal_purchase_order', 15, 2)->nullable();

            // check-out
            $table->time('check_out')->nullable();
            $table->string('coordinate_check_out')->nullable(); // lat,lng gabungan

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
