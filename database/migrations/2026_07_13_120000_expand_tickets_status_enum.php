<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Widen tickets.status from (open, in_progress, closed) to the full
     * workflow set. Enum modification is MySQL-specific; SQLite (used by the
     * test suite) stores the column as TEXT with no constraint, so it needs
     * no change there.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE tickets MODIFY status ENUM('open','in_progress','on_hold','waiting_for_resources','rejected','closed','resolved') NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Fold the new states back into 'closed' so the narrowed enum accepts them.
        DB::statement("UPDATE tickets SET status = 'closed' WHERE status IN ('on_hold','waiting_for_resources','rejected','resolved')");
        DB::statement("ALTER TABLE tickets MODIFY status ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open'");
    }
};
