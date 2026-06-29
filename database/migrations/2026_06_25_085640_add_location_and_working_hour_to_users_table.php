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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->after('role')->constrained('locations')->nullOnDelete();
            $table->foreignId('working_hour_id')->nullable()->after('location_id')->constrained('working_hours')->nullOnDelete();
            $table->boolean('is_location_locked')->default(true)->after('working_hour_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['working_hour_id']);
            $table->dropColumn(['location_id', 'working_hour_id', 'is_location_locked']);
        });
    }
};
