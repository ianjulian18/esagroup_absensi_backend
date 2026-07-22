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
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            // Menyambungkan data laporan ke karyawan yang sedang login
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Kolom wajib laporan
            $table->string('store_name'); // Nama toko/lokasi kunjungan
            $table->text('issue');        // Masalah yang ditemukan
            $table->text('action');       // Tindakan yang dilakukan
            $table->string('target');     // Target penyelesaian
            $table->string('actual');     // Hasil aktual di lapangan
            $table->date('deadline');     // Tenggat waktu
            $table->text('notes')->nullable(); // Catatan tambahan (opsional)
            
            // Status Tracking (Default otomatis 'open' saat karyawan mengirim dari HP)
            $table->enum('status', ['open', 'action_taken', 'completed', 'overdue'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
