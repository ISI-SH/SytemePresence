<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_tokens', function (Blueprint $table) {
            $table->dropUnique('daily_tokens_date_unique');
            $table->timestamp('valid_from')->nullable()->after('token');
            $table->index('expires_at');
        });

        DB::table('daily_tokens')->delete();

        Schema::table('daily_tokens', function (Blueprint $table) {
            $table->unique('valid_from');
        });
    }

    public function down(): void
    {
        Schema::table('daily_tokens', function (Blueprint $table) {
            $table->dropUnique(['valid_from']);
            $table->dropIndex(['expires_at']);
            $table->dropColumn('valid_from');
            $table->unique('date');
        });
    }
};
