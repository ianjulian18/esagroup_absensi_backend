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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel attendances (Satu kali absen harian bisa punya banyak visit)
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            
            $table->string('visit_type')->default('store'); 
            $table->string('location_name')->nullable(); // Nama toko / lokasi visit
            
            $table->time('visit_in')->nullable();
            $table->time('visit_out')->nullable();
            
            $table->string('latitude_in')->nullable();
            $table->string('longitude_in')->nullable();
            $table->string('photo_in')->nullable();
            
            $table->string('latitude_out')->nullable();
            $table->string('longitude_out')->nullable();
            $table->string('photo_out')->nullable();
            
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
