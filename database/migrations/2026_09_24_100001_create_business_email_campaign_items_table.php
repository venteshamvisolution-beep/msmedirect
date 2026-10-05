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
        Schema::create('business_email_campaign_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('business_email_campaigns')->cascadeOnDelete();
            $table->unsignedBigInteger('business_data_id');
            $table->string('email');
            $table->string('status')->default('pending'); // pending, queued, sending, sent, failed
            $table->integer('attempts')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'business_data_id'], 'beci_campaign_bdata_unique');
            $table->index(['campaign_id', 'status']);
            $table->index('business_data_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_email_campaign_items');
    }
};
