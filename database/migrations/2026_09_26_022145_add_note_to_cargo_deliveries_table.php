<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('cargo_deliveries', 'note')) {
            Schema::table('cargo_deliveries', function (Blueprint $table) {
                $table->text('note')->nullable()->after('package_note');
            });
        }

        // Sao chép nội dung ghi chú người dùng cũ sang cột note
        DB::statement('UPDATE cargo_deliveries SET note = package_note WHERE package_note IS NOT NULL');

        // Cập nhật lại thông tin ghi bao chuẩn từ bảng groups
        DB::statement('
            UPDATE cargo_deliveries cd
            JOIN `groups` g ON cd.group_id = g.id
            SET cd.package_note = g.package_note
        ');
    }

    public function down(): void
    {
        Schema::table('cargo_deliveries', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
