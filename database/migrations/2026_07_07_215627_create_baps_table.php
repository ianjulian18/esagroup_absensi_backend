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
        Schema::create('baps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date'); // Tanggal absen yang terlewat
            $table->enum('type', ['masuk', 'pulang']); // Jenis absen
            $table->time('time'); // Jam yang seharusnya
            $table->text('reason'); // Alasan lupa/gagal absen
            $table->string('proof_path')->nullable(); // Foto bukti (misal: foto dari pos satpam, dsb)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baps');
    }
};
