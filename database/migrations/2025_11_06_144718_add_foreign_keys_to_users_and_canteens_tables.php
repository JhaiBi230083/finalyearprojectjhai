<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('canteen_id')
                ->references('id')
                ->on('canteens')
                ->onDelete('set null');
        });

        Schema::table('canteens', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['canteen_id']);
        });

        Schema::table('canteens', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
        });
    }
};
