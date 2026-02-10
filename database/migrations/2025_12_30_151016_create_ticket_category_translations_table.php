<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_category_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_category_translations');
    }
};
