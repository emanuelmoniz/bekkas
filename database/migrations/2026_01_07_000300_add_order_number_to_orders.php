<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Order;

return new class extends Migration
{
    public function up(): void
    {
        // Add column only if it doesn't exist
        if (!Schema::hasColumn('orders', 'order_number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_number')->nullable()->after('id');
            });
        }

        // Generate order numbers for existing orders that don't have one
        $orders = Order::whereNull('order_number')->get();
        foreach ($orders as $order) {
            do {
                $orderNumber = 'ORD-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
            } while (Order::where('order_number', $orderNumber)->exists());
            
            $order->update(['order_number' => $orderNumber]);
        }

        // Add unique constraint if it doesn't exist
        try {
            Schema::table('orders', function (Blueprint $table) {
                $table->unique('order_number');
            });
        } catch (\Exception $e) {
            // Unique constraint already exists, ignore
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['order_number']);
            $table->dropColumn('order_number');
        });
    }
};
