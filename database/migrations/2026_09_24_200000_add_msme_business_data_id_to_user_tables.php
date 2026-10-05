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
        if (Schema::hasTable('user_data') && !Schema::hasColumn('user_data', 'msme_business_data_id')) {
            Schema::table('user_data', function (Blueprint $table) {
                $table->unsignedBigInteger('msme_business_data_id')->nullable()->after('id')->index();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'msme_business_data_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('msme_business_data_id')->nullable()->after('id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_data') && Schema::hasColumn('user_data', 'msme_business_data_id')) {
            Schema::table('user_data', function (Blueprint $table) {
                $table->dropColumn('msme_business_data_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'msme_business_data_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('msme_business_data_id');
            });
        }
    }
};
