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
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('period'); // Periode Gaji (Misal: 2026-07-01 untuk gaji Juli 2026)
            $table->bigInteger('basic_salary'); // Gaji Pokok
            $table->bigInteger('allowances')->default(0); // Tunjangan (Transport, Makan, dll)
            $table->bigInteger('deductions')->default(0); // Potongan (BPJS, Telat, Alpha)
            $table->bigInteger('overtime_pay')->default(0); // Uang Lembur
            $table->bigInteger('net_salary'); // Gaji Bersih (Total yang diterima)
            $table->enum('status', ['draft', 'published'])->default('draft'); // Draft = belum bisa dilihat karyawan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
