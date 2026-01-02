<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');

            $table->foreignId('ticket_category_id')->constrained();

            $table->string('title');
            $table->enum('status', ['open', 'closed'])->default('open');

            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();
            $table->text('close_reason')->nullable();
            $table->text('reopen_reason')->nullable();

            $table->date('due_date')->nullable();
            $table->dateTime('last_message_at')->nullable();

            $table->json('read_state')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
