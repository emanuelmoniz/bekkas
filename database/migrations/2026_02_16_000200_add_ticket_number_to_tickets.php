<?php

use App\Models\Ticket;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tickets', 'ticket_number')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('ticket_number')->nullable()->after('id');
            });
        }

        // Backfill existing tickets
        $tickets = Ticket::whereNull('ticket_number')->get();
        foreach ($tickets as $ticket) {
            do {
                $ticketNumber = 'TCK-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
            } while (Ticket::where('ticket_number', $ticketNumber)->exists());

            $ticket->update(['ticket_number' => $ticketNumber]);
        }

        // Unique constraint
        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unique('ticket_number');
            });
        } catch (\Exception $e) {
            // ignore if constraint already exists
        }
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique(['ticket_number']);
            $table->dropColumn('ticket_number');
        });
    }
};
