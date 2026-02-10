<?php

$loader = require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$order = \App\Models\Order::where('uuid', '7e2b1f79-4801-4be6-be5e-dd1ff801622d')->first();
if (! $order) {
    echo "no order\n";
    exit(0);
}
$sessions = \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->orderBy('updated_at', 'desc')->limit(5)->get();
foreach ($sessions as $s) {
    echo $s->id.'|'.$s->checkout_id.'|'.$s->session_id.'|is_active:'.($s->is_active ? '1' : '0').'|status:'.$s->status.'|in_error:'.($s->in_error ? '1' : '0')."\n";
    echo ($s->message ?: '')."\n\n";
}
