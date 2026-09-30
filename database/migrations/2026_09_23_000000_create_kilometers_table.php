<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kilometers', function (Blueprint $table) {
            $table->id();
            $table->date('ngay');
            $table->string('nhom', 100);
            $table->string('dia_chi');
            $table->string('phuong', 100);
            $table->string('quan', 100);
            $table->decimal('km', 8, 2);
            $table->decimal('tien_chanh', 12, 2)->default(0);
            $table->string('xe_om', 150)->nullable();
            $table->text('thong_tin_ghi_bao')->nullable();
            $table->timestamps();

            $table->index(['ngay', 'nhom']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kilometers');
    }
};
