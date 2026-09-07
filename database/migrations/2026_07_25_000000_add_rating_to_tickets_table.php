<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Requester satisfaction rating (1-5), left by the employee once the
            // ticket reaches a solved terminal state. Nullable = not yet rated.
            $table->unsignedTinyInteger('rating')->nullable()->after('agent_id');
            $table->text('rating_comment')->nullable()->after('rating');
            $table->timestamp('rated_at')->nullable()->after('rating_comment');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_comment', 'rated_at']);
        });
    }
};
