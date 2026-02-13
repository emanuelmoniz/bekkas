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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();

            $table->text('app_name')->nullable();

            $table->boolean('store_enabled')->default(true);
            $table->boolean('send_mails_enabled')->default(true);
            $table->boolean('easypay_enabled')->default(false);

            $table->text('mail_admin')->nullable();
            $table->text('mail_contact')->nullable();

            $table->text('smtp_server_host')->nullable();
            $table->text('smtp_server_port')->nullable();
            $table->text('smtp_username')->nullable();
            $table->text('smtp_password')->nullable();
            $table->text('smtp_encryptation')->nullable();
            $table->text('smtp_mail_from')->nullable();

            $table->text('google_recaptcha_site_key')->nullable();
            $table->text('google_recaptcha_secret_key')->nullable();

            $table->text('easypay_api_key')->nullable();
            $table->text('easypay_id')->nullable();
            $table->text('easypay_webhook_secret')->nullable();
            $table->text('easypay_webhook_header')->nullable();
            $table->text('easypay_webhook_user')->nullable();
            $table->text('easypay_webhook_pass')->nullable();
            $table->text('easypay_url_url')->nullable();
            $table->text('easypay_sdk_url')->nullable();
            $table->text('easypay_payment_methods')->nullable();

            $table->integer('easypay_session_ttl')->nullable();
            $table->integer('easypay_mb_ttl')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
