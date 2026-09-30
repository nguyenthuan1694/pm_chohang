<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kilometers', function (Blueprint $table) {
            if (!Schema::hasColumn('kilometers', 'owner_user_id')) {
                $table->foreignId('owner_user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
                $table->index('owner_user_id');
            }
            if (!Schema::hasColumn('kilometers', 'group_id')) {
                $table->unsignedBigInteger('group_id')->nullable()->after('owner_user_id');
                $table->index('group_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kilometers', function (Blueprint $table) {
            if (Schema::hasColumn('kilometers', 'group_id')) {
                $table->dropIndex(['group_id']);
                $table->dropColumn('group_id');
            }
            if (Schema::hasColumn('kilometers', 'owner_user_id')) {
                $table->dropConstrainedForeignId('owner_user_id');
            }
        });
    }
};
