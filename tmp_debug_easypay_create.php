<?php
// Temporary debug script — prints DB queries and stack traces when easypay rows are created
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

Config::set('easypay.enabled', false);

DB::listen(function ($query) {
    $sql = $query->sql;
    if (stripos($sql, 'insert into "easypay_payloads"') !== false || stripos($sql, 'insert into `easypay_payloads`') !== false) {
        echo "-- Detected easypay_payloads INSERT:\n";
        echo $sql . "\n";
        // print a short stack
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $count = 0;
        foreach ($bt as $frame) {
            if (!empty($frame['file'])) {
                echo "#" . $count++ . " " . ($frame['file'] ?? '') . ':' . ($frame['line'] ?? '') . " -> " . ($frame['function'] ?? '') . "\n";
                if ($count > 8) break;
            }
        }
    }
});

// Create user + order (mirror failing test)
$user = \App\Models\User::factory()->create(['language' => 'pt']);
$order = \App\Models\Order::factory()->for($user)->create(['status' => 'WAITING_PAYMENT', 'is_paid' => false]);

echo "Order created: id={$order->id}, uuid={$order->uuid}\n";
$payloads = DB::table('easypay_payloads')->where('order_id', $order->id)->get();
$sessions = DB::table('easypay_checkout_sessions')->where('order_id', $order->id)->get();

echo "easypay_payloads count: " . $payloads->count() . "\n";
if ($payloads->count()) print_r($payloads->toArray());

echo "easypay_checkout_sessions count: " . $sessions->count() . "\n";
if ($sessions->count()) print_r($sessions->toArray());

// cleanup
\App\Models\EasypayPayload::where('order_id', $order->id)->delete();
\App\Models\EasypayCheckoutSession::where('order_id', $order->id)->delete();
\App\Models\EasypayPayment::where('order_id', $order->id)->delete();

echo "Done.\n";
