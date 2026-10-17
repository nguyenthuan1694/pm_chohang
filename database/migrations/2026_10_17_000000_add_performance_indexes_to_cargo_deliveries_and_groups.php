<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cargo_deliveries', function (Blueprint $table) {
            $table->index(['trip_count', 'updated_at', 'id'], 'cd_trip_updated_id_idx');
            $table->index(['delivery_status', 'id'], 'cd_status_id_idx');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->index(['status', 'group_date', 'id'], 'groups_status_date_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('cargo_deliveries', function (Blueprint $table) {
            $table->dropIndex('cd_trip_updated_id_idx');
            $table->dropIndex('cd_status_id_idx');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropIndex('groups_status_date_id_idx');
        });
    }
};
