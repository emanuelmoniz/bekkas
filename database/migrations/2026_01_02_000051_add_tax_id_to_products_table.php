<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT products_tax_id_foreign
            FOREIGN KEY (tax_id) REFERENCES taxes(id)
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE products
            DROP FOREIGN KEY products_tax_id_foreign
        ");
    }
};
