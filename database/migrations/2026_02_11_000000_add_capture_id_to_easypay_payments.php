<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('easypay_payments', function (Blueprint $table) {
            $table->string('capture_id')->nullable()->after('payment_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('easypay_payments', function (Blueprint $table) {
            $table->dropIndex(['capture_id']);
            $table->dropColumn('capture_id');
        });
    }
};
