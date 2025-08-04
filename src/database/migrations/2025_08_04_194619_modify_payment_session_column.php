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
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['payment_session']);
            $table->dropColumn('payment_session');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->text('payment_session')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('merchant_order_id');
            $table->string('merchant_order_id')->nullable()->unique();
        });
    }
};
