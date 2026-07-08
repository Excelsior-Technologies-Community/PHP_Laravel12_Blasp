<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('reputation_score')->default(100)->after('password');
            $table->timestamp('muted_until')->nullable()->after('reputation_score');
            $table->integer('profanity_hits')->default(0)->after('muted_until');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reputation_score', 'muted_until', 'profanity_hits']);
        });
    }
};
