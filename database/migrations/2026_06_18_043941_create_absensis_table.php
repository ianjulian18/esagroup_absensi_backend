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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            
            // Kolom penyambung: Absensi ini milik karyawan siapa?
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Kolom pencatat waktu & lokasi
            $table->date('tanggal'); 
            $table->time('jam_masuk')->nullable(); 
            $table->time('jam_pulang')->nullable(); 
            $table->string('lokasi_masuk')->nullable(); 
            $table->string('lokasi_pulang')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
