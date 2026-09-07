<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Original sender of an email-to-ticket message. Null for tickets
            // submitted through the web form. Lets admins see the real sender
            // even when the ticket is attributed to the External Sender account.
            $table->string('source_email')->nullable()->after('agent_id');
            $table->string('source_name')->nullable()->after('source_email');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['source_email', 'source_name']);
        });
    }
};
