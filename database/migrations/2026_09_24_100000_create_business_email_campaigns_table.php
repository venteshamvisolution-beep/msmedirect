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
        Schema::create('business_email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('queued_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->unsignedInteger('pending_count')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_email_campaigns');
    }
};
