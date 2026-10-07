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
        $fields = [
            'company' => fn(Blueprint $table) => $table->string('company')->nullable(),
            'person_name' => fn(Blueprint $table) => $table->string('person_name')->nullable(),
            'office_no' => fn(Blueprint $table) => $table->string('office_no')->nullable(),
            'mobile_no' => fn(Blueprint $table) => $table->string('mobile_no')->nullable(),
            'website' => fn(Blueprint $table) => $table->string('website')->nullable(),
            'details' => fn(Blueprint $table) => $table->text('details')->nullable(),
        ];

        foreach (['user_data', 'new_user_data'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $fields) {
                    foreach ($fields as $column => $columnCallback) {
                        if (!Schema::hasColumn($tableName, $column)) {
                            $columnCallback($table);
                        }
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = ['company', 'person_name', 'office_no', 'mobile_no', 'website', 'details'];

        foreach (['user_data', 'new_user_data'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                    foreach ($columns as $column) {
                        if (Schema::hasColumn($tableName, $column)) {
                            $table->dropColumn($column);
                        }
                    }
                });
            }
        }
    }
};
