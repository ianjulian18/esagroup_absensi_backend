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
        Schema::create('working_group_schedules', function (Blueprint $table) {
            $table->id();
            // Relasi ke grup induk
            $table->foreignId('working_group_id')->constrained()->cascadeOnDelete();
            
            $table->string('day_of_week'); // Senin, Selasa, Rabu, dll
            
            // Relasi ke tabel WorkingHours (Shift) yang sudah pernah kamu buat sebelumnya
            $table->foreignId('working_hour_id')->nullable()->constrained()->nullOnDelete();
            
            $table->integer('late_tolerance')->default(15); // Batas telat dalam menit
            $table->enum('routing_type', ['bebas_visit', 'routing_aktif'])->default('bebas_visit');
            $table->json('stores')->nullable(); // Menyimpan daftar toko [Toko A, Toko B, dst]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_group_schedules');
    }
};
