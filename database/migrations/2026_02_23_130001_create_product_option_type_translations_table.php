<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('product_option_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_type_id')->constrained()->cascadeOnDelete();
            $table->string('locale');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->unique(['product_option_type_id', 'locale'], 'pot_trans_type_locale_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_option_type_translations');
    }
};
