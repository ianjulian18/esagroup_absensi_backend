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
            $table->foreignId('entity_id')->nullable()->constrained('entities')->nullOnDelete();
        });
        
        Schema::table('working_groups', function (Blueprint $table) {
            $table->foreignId('entity_id')->nullable()->constrained('entities')->nullOnDelete();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->foreignId('entity_id')->nullable()->constrained('entities')->nullOnDelete();
            $table->foreignId('principal_id')->nullable()->constrained('principals')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn('entity_id');
        });
        
        Schema::table('working_groups', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn('entity_id');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn('entity_id');
            $table->dropForeign(['principal_id']);
            $table->dropColumn('principal_id');
        });
    }
};
