<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('easypay_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_id')->index();
            $table->string('checkout_id')->nullable()->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('payment_status')->nullable()->index();
            $table->timestamp('paid_at')->nullable()->index();
            $table->string('payment_method')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_last_digits')->nullable();
            $table->string('mb_entity')->nullable();
            $table->string('mb_reference')->nullable();
            $table->timestamp('mb_expiration_time')->nullable();
            $table->string('iban')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('easypay_payments');
    }
};
