<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'uncategorized' to tickets.category so an email we couldn't classify
     * is left for a technician instead of being guessed into 'software'. Enum
     * modification is MySQL-specific; SQLite (used by the test suite) already
     * picks up the new value from the create-tickets migration, so it needs no
     * change here.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE tickets MODIFY category ENUM('network','hardware','software','access_request','uncategorized') NOT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        // Fold the new value back into 'software' so the narrowed enum accepts it.
        DB::statement("UPDATE tickets SET category = 'software' WHERE category = 'uncategorized'");
        DB::statement("ALTER TABLE tickets MODIFY category ENUM('network','hardware','software','access_request') NOT NULL");
    }
};
