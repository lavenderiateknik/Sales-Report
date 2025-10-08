<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_database', function (Blueprint $table) { // 🟢 gunakan underscore (konvensi Laravel)
            $table->id();

            // Relasi ke branches
            $table->foreignId('id_branch')->nullable()->constrained('branches')->nullOnDelete();

            // Data proyek
            $table->string('project_id')->nullable();
            $table->string('project_type')->nullable();
            $table->string('development_type')->nullable();
            $table->text('project_name')->nullable();
            $table->longText('project_detail')->nullable();
            $table->string('project_stage')->nullable();
            $table->string('project_region')->nullable();
            $table->string('project_street_name')->nullable();
            $table->string('local_value')->nullable();
            $table->date('construction_start_date')->nullable();
            $table->date('construction_end_date')->nullable();
            $table->string('floor_area')->nullable();
            $table->string('site_area')->nullable();
            $table->string('storeys')->nullable();
            $table->string('role_on_project')->nullable();
            $table->date('last_update')->nullable();
            $table->text('project_address')->nullable();
            $table->string('project_town')->nullable();
            $table->string('company_website')->nullable();
            $table->string('project_province')->nullable();
            $table->string('post_code')->nullable();
            $table->string('company_name')->nullable();
            $table->text('company_street_name')->nullable();
            $table->text('company_roles')->nullable();
            $table->string('company_town')->nullable();
            $table->string('company_state')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('contact_first_name')->nullable();
            $table->string('contact_surname')->nullable();
            $table->string('contact_position')->nullable();
            $table->string('contact_landline')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('contact_remark')->nullable();
            $table->string('company_region')->nullable();
            $table->string('mobile')->nullable();
            $table->string('role_status')->nullable();
            $table->string('construction_start_text')->nullable();
            $table->string('construction_end_text')->nullable();
            // 🟢 Tambahan untuk deteksi duplikat
            $table->string('hash', 64)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_database');
    }
};
