<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cargo_deliveries', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('id')->constrained('groups')->cascadeOnUpdate()->restrictOnDelete();
            $table->index('group_id');
        });
    }

    public function down(): void
    {
        Schema::table('cargo_deliveries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('group_id');
        });
    }
};
