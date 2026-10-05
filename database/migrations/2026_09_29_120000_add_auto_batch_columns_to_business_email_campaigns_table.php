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
        Schema::table('business_email_campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->unsignedBigInteger('start_id')->nullable()->after('parent_id');
            $table->unsignedBigInteger('end_id')->nullable()->after('start_id');
            $table->unsignedInteger('batch_size')->nullable()->after('end_id');
            $table->unsignedBigInteger('max_id')->nullable()->after('batch_size');
            $table->unsignedInteger('skipped_count')->default(0)->after('total_count');
            $table->text('error_log')->nullable()->after('completed_at');

            $table->foreign('parent_id')->references('id')->on('business_email_campaigns')->nullOnDelete();
            $table->index('parent_id');
            $table->index(['start_id', 'end_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_email_campaigns', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'start_id',
                'end_id',
                'batch_size',
                'max_id',
                'skipped_count',
                'error_log',
            ]);
        });
    }
};
