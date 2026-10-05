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
        Schema::table('business_email_campaign_items', function (Blueprint $table) {
            $table->string('message_id')->nullable()->index()->after('email');
            $table->string('delivery_status')->default('pending')->index()->after('status');
            $table->timestamp('delivered_at')->nullable()->after('sent_at');
        });

        Schema::table('business_email_campaigns', function (Blueprint $table) {
            $table->unsignedInteger('delivered_count')->default(0)->after('sent_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_email_campaign_items', function (Blueprint $table) {
            $table->dropColumn(['message_id', 'delivery_status', 'delivered_at']);
        });

        Schema::table('business_email_campaigns', function (Blueprint $table) {
            $table->dropColumn(['delivered_count']);
        });
    }
};
