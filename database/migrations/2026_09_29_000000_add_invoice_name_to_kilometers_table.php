<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kilometers', function (Blueprint $table) {
            if (!Schema::hasColumn('kilometers', 'invoice_name')) {
                $table->string('invoice_name', 150)->nullable()->after('group_id');
                $table->index('invoice_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kilometers', function (Blueprint $table) {
            if (Schema::hasColumn('kilometers', 'invoice_name')) {
                $table->dropIndex(['invoice_name']);
                $table->dropColumn('invoice_name');
            }
        });
    }
};
