<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing easypay_payloads -> payload->customer->language to ISO 639-1 alpha-2 (uppercase)
        $rows = DB::table('easypay_payloads')->select('id', 'payload')->get();
        $updated = 0;

        foreach ($rows as $row) {
            $payload = json_decode($row->payload, true);
            if (! is_array($payload)) continue;

            $lang = $payload['customer']['language'] ?? null;
            if (! $lang || ! is_string($lang)) continue;

            $new = strtoupper(substr($lang, 0, 2));
            if ($new === $lang) continue;

            $payload['customer']['language'] = $new;

            DB::table('easypay_payloads')
                ->where('id', $row->id)
                ->update(['payload' => json_encode($payload)]);

            $updated++;
        }

        Log::info('Normalize Easypay payload languages migration ran', ['rows_checked' => $rows->count(), 'rows_updated' => $updated]);
    }

    public function down(): void
    {
        // irreversible data normalization — no-op
    }
};
