<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_option_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained()->cascadeOnDelete();
            $table->string('locale');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->unique(['product_option_id', 'locale'], 'pot_opt_type_locale_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_option_translations');
    }
};
