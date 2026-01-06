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
       Schema::create('attendance_summaries', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

        $table->unsignedTinyInteger('month'); // 1-12
        $table->unsignedSmallInteger('year');

        $table->unsignedTinyInteger('working_days');   // hari kerja
        $table->unsignedTinyInteger('present_days');   // hadir
        $table->unsignedTinyInteger('absent_days')->default(0); // tidak hadir

        $table->foreignId('created_by')->constrained('users'); // SPV/Admin

        $table->timestamps();

        $table->unique(['user_id','month','year']);
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_summaries');
    }
};
