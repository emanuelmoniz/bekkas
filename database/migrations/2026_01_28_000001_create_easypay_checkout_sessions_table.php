<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('easypay_checkout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('checkout_id')->nullable()->index();
            $table->text('session_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('payload_id')->nullable()->constrained('easypay_payloads')->nullOnDelete();
            $table->boolean('in_error')->default(false);
            $table->integer('error_code')->nullable();
            $table->string('status')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('timestamp')->useCurrent();
            $table->timestamp('last_update_timestamp')->nullable()->useCurrentOnUpdate();
            $table->timestamps();

            $table->index(['order_id', 'in_error']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('easypay_checkout_sessions');
    }
};
