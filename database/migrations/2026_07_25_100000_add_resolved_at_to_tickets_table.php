<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Timestamp the ticket first reached a solved state (closed/resolved),
            // so reports can measure resolution time. Null = never solved yet.
            $table->timestamp('resolved_at')->nullable()->after('rated_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('resolved_at');
        });
    }
};
