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
        // Drop existing FK, make column nullable and add FK with ON DELETE SET NULL
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        // Altering column syntax differs between DB engines. Run raw ALTER only for MySQL.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `tickets` MODIFY `created_by` BIGINT UNSIGNED NULL');

            Schema::table('tickets', function (Blueprint $table) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        } else {
            // SQLite / others: recreate the table with `created_by` nullable and ON DELETE SET NULL
            // so behavior matches MySQL and tests behave consistently.
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement(<<<'SQL'
CREATE TABLE tickets_new (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ticket_number VARCHAR(191) NULL,
    uuid VARCHAR(36) NOT NULL UNIQUE,
    user_id INTEGER NOT NULL,
    created_by INTEGER NULL,
    ticket_category_id INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    status VARCHAR(10) NOT NULL DEFAULT 'open',
    opened_at DATETIME NOT NULL,
    closed_at DATETIME NULL,
    close_reason TEXT NULL,
    reopen_reason TEXT NULL,
    due_date DATE NULL,
    last_message_at DATETIME NULL,
    read_state TEXT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY(ticket_category_id) REFERENCES ticket_categories(id)
);
SQL
            );

            DB::statement('INSERT INTO tickets_new (id, uuid, user_id, created_by, ticket_category_id, title, status, opened_at, closed_at, close_reason, reopen_reason, due_date, last_message_at, read_state, created_at, updated_at) SELECT id, uuid, user_id, created_by, ticket_category_id, title, status, opened_at, closed_at, close_reason, reopen_reason, due_date, last_message_at, read_state, created_at, updated_at FROM tickets');

            DB::statement('DROP TABLE tickets');
            DB::statement('ALTER TABLE tickets_new RENAME TO tickets');

            // recreate unique index for ticket_number (added by earlier migration)
            try {
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS tickets_ticket_number_unique ON tickets(ticket_number)');
            } catch (\Exception $e) {
                // ignore if index cannot be created
            }

            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        // Ensure no nulls remain — set them to owner (user_id) where null
        DB::statement('UPDATE `tickets` SET `created_by` = `user_id` WHERE `created_by` IS NULL');

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `tickets` MODIFY `created_by` BIGINT UNSIGNED NOT NULL');
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
};
