<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            // Nullable so historical order items without options still work
            $table->foreignId('product_option_id')->nullable()->constrained()->nullOnDelete();
            // Snapshots so the record remains accurate even after options are renamed/deleted
            $table->string('option_type_name');
            $table->string('option_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
    }
};
