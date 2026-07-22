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
        Schema::create('working_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Misal: "Sales Area Jatim"
            $table->string('region')->nullable();
            $table->string('area')->nullable();
            $table->date('date_applied'); // Tanggal mulai berlaku aturan ini
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_groups');
    }
};
