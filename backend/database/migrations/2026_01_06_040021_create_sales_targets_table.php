<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');

            $table->decimal('target_omset', 15, 2)->default(0);
            $table->unsignedInteger('target_visit')->default(0);
            $table->unsignedInteger('target_penawaran')->default(0);
            $table->unsignedInteger('target_new_customer')->default(0);

            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['user_id','month','year']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
