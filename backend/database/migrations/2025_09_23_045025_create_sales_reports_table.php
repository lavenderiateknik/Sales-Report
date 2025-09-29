<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();

            // tanggal laporan
            $table->date('date');

            // relasi ke users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // check-in
            $table->time('check_in');
            $table->string('coordinate_check_in')->nullable(); // lat,lng

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

            // kebutuhan tambahan
            $table->text('equipment_needs')->nullable();
            $table->text('items_purchase_order')->nullable();
            $table->decimal('nominal_purchase_order', 15, 2)->nullable();

            // check-out
            $table->time('check_out')->nullable();
            $table->string('coordinate_check_out')->nullable(); // lat,lng

            // sementara binary, nanti diubah ke LONGBLOB
            $table->binary('picture')->nullable();

            $table->timestamps();
        });

        // ubah kolom picture menjadi LONGBLOB (supaya support >64KB)
        DB::statement('ALTER TABLE sales_reports MODIFY picture LONGBLOB NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
