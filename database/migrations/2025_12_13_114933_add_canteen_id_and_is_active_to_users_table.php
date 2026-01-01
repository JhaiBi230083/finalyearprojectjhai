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
            // Add canteen_id foreign key if it doesn't exist
            if (!Schema::hasColumn('users', 'canteen_id')) {
                $table->foreignId('canteen_id')
                    ->nullable()
                    ->after('matric_number')
                    ->constrained('canteens')
                    ->onDelete('set null');
            }

            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('canteen_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['canteen_id']);
            $table->dropColumn(['canteen_id', 'is_active']);
        });
    }
};
