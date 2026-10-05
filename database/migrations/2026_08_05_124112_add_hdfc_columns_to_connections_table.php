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
        Schema::table('connections', function (Blueprint $table) {
            $table->string('conn_hdfc_order_id')->nullable();
            $table->string('from_hdfc_order_id')->nullable();
            $table->string('conn_hdfc_payment_id')->nullable();
            $table->string('from_hdfc_payment_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connections', function (Blueprint $table) {
            $table->dropColumn([
                'conn_hdfc_order_id', 
                'from_hdfc_order_id', 
                'conn_hdfc_payment_id', 
                'from_hdfc_payment_id'
            ]);
        });
    }
};
