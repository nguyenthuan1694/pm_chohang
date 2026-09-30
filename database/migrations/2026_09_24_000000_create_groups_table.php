<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kilometer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('group_date');
            $table->string('invoice_name');
            $table->string('group_name', 100);
            $table->string('address');
            $table->string('ward', 100);
            $table->string('district', 100);
            $table->decimal('distance_km', 8, 2);
            $table->decimal('carrier_fee', 12, 2)->default(0);
            $table->string('motorbike_driver', 150)->nullable();
            $table->text('package_note')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();

            $table->index(['group_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
