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
        if (Schema::hasTable('user_data') && !Schema::hasColumn('user_data', 'is_bulk_user')) {
            Schema::table('user_data', function (Blueprint $table) {
                $table->boolean('is_bulk_user')->default(false);
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'is_bulk_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_bulk_user')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_data') && Schema::hasColumn('user_data', 'is_bulk_user')) {
            Schema::table('user_data', function (Blueprint $table) {
                $table->dropColumn('is_bulk_user');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'is_bulk_user')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_bulk_user');
            });
        }
    }
};
