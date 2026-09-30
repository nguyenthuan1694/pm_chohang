<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('kilometers', 'ngay')) {
            Schema::table('kilometers', function (Blueprint $table) {
                // Drop old composite index if exists
                $table->dropIndex('kilometers_ngay_nhom_index');
                $table->dropColumn('nhom');
                $table->renameColumn('ngay', 'date');
                $table->renameColumn('dia_chi', 'address');
                $table->renameColumn('phuong', 'ward');
                $table->renameColumn('quan', 'district');
                $table->renameColumn('km', 'distance_km');
                $table->renameColumn('tien_chanh', 'carrier_fee');
                $table->renameColumn('xe_om', 'motorbike_driver');
                $table->renameColumn('thong_tin_ghi_bao', 'package_note');
                $table->index('date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kilometers', 'date')) {
            Schema::table('kilometers', function (Blueprint $table) {
                $table->dropIndex(['date']);
                $table->string('nhom', 100)->after('id');
                $table->renameColumn('date', 'ngay');
                $table->renameColumn('address', 'dia_chi');
                $table->renameColumn('ward', 'phuong');
                $table->renameColumn('district', 'quan');
                $table->renameColumn('distance_km', 'km');
                $table->renameColumn('carrier_fee', 'tien_chanh');
                $table->renameColumn('motorbike_driver', 'xe_om');
                $table->renameColumn('package_note', 'thong_tin_ghi_bao');
                $table->index(['ngay', 'nhom']);
            });
        }
    }
};
