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
        Schema::table('working_groups', function (Blueprint $table) {
            // 1. Tambahan hierarki wilayah
            $table->string('sub_area')->nullable()->after('area');
            
            // 2. Aturan General (Baseline / Default) sesuai PDF
            $table->foreignId('default_working_hour_id')->nullable()->constrained('working_hours')->nullOnDelete();
            $table->integer('default_late_tolerance')->default(15);
            $table->json('default_stores')->nullable();
            
            // 3. First Visit Lock (Pengunci toko wajib pertama)
            $table->boolean('is_first_visit_locked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_groups', function (Blueprint $table) {
            $table->dropForeign(['default_working_hour_id']);
            $table->dropColumn([
                'sub_area',
                'default_working_hour_id', 
                'default_late_tolerance', 
                'default_stores', 
                'is_first_visit_locked'
            ]);
        });
    }
};
