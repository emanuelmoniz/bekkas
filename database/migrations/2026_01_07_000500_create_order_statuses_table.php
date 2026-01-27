<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('order_status_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_status_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('name');
            $table->timestamps();

            $table->unique(['order_status_id', 'locale']);
        });

        // Insert default statuses
        $now = now();
        $statuses = [
            ['code' => 'WAITING_PAYMENT', 'sort_order' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'PROCESSING', 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'DISPATCHED', 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'DELIVERED', 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'CANCELED', 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($statuses as $status) {
            $id = DB::table('order_statuses')->insertGetId($status);

            // Add translations
            DB::table('order_status_translations')->insert([
                ['order_status_id' => $id, 'locale' => 'pt-PT', 'name' => $this->getPortugueseName($status['code']), 'created_at' => $now, 'updated_at' => $now],
                ['order_status_id' => $id, 'locale' => 'en-UK', 'name' => $this->getEnglishName($status['code']), 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
    }

    private function getPortugueseName($code)
    {
        return match($code) {
            'WAITING_PAYMENT' => 'Aguardando Pagamento',
            'PROCESSING' => 'Em Processamento',
            'DISPATCHED' => 'Enviado',
            'DELIVERED' => 'Entregue',
            'CANCELED' => 'Cancelado',
            default => $code,
        };
    }

    private function getEnglishName($code)
    {
        return match($code) {
            'WAITING_PAYMENT' => 'Waiting Payment',
            'PROCESSING' => 'Processing',
            'DISPATCHED' => 'Dispatched',
            'DELIVERED' => 'Delivered',
            'CANCELED' => 'Canceled',
            default => $code,
        };
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_translations');
        Schema::dropIfExists('order_statuses');
    }
};
