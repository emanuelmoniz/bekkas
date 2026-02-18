<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            // Google social login
            $table->boolean('google_socialite_enabled')->default(false)->after('google_recaptcha_secret_key');
            $table->text('google_client_id')->nullable()->after('google_socialite_enabled');
            $table->text('google_client_secret')->nullable()->after('google_client_id');
            $table->text('google_redirect')->nullable()->after('google_client_secret');

            // Microsoft social login
            $table->boolean('microsoft_socialite_enabled')->default(false)->after('google_redirect');
            $table->text('microsoft_client_id')->nullable()->after('microsoft_socialite_enabled');
            $table->text('microsoft_client_secret')->nullable()->after('microsoft_client_id');
            $table->text('microsoft_redirect')->nullable()->after('microsoft_client_secret');
            $table->text('microsoft_tenant')->nullable()->after('microsoft_redirect');
        });

        // Seed existing configuration row with env values as a one-time migration convenience.
        if (Schema::hasTable('configurations')) {
            $row = DB::table('configurations')->orderBy('id', 'desc')->first();

            $values = [
                'google_socialite_enabled' => env('GOOGLE_SOCIALITE_ENABLED', false) ? 1 : 0,
                'google_client_id' => env('GOOGLE_CLIENT_ID'),
                'google_client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'google_redirect' => env('GOOGLE_REDIRECT'),

                'microsoft_socialite_enabled' => env('MICROSOFT_SOCIALITE_ENABLED', false) ? 1 : 0,
                'microsoft_client_id' => env('MICROSOFT_CLIENT_ID'),
                'microsoft_client_secret' => env('MICROSOFT_CLIENT_SECRET'),
                'microsoft_redirect' => env('MICROSOFT_REDIRECT'),
                'microsoft_tenant' => env('MICROSOFT_TENANT'),
            ];

            // Only set values that are non-null to avoid overwriting deliberate DB values.
            $filtered = array_filter($values, function ($v) { return $v !== null && $v !== ''; });

            if ($row) {
                DB::table('configurations')->where('id', $row->id)->update(array_merge($filtered, ['updated_at' => now()]));
            } else {
                DB::table('configurations')->insert(array_merge($filtered, ['created_at' => now(), 'updated_at' => now()]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn([
                'google_socialite_enabled',
                'google_client_id',
                'google_client_secret',
                'google_redirect',
                'microsoft_socialite_enabled',
                'microsoft_client_id',
                'microsoft_client_secret',
                'microsoft_redirect',
                'microsoft_tenant',
            ]);
        });
    }
};
