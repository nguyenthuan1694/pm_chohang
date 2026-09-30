<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cargo_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('kilometer_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->dateTime('delivery_date');
            $table->string('invoice_name');
            $table->string('group_name', 100);
            $table->string('address');
            $table->string('ward', 100);
            $table->string('district', 100);
            $table->unsignedInteger('trip_count');
            $table->string('package_count', 50);
            $table->decimal('weight_kg', 10, 2);
            $table->decimal('distance_km', 8, 2);
            $table->decimal('carrier_fee', 12, 2)->default(0);
            $table->string('motorbike_driver', 150)->nullable();
            $table->text('package_note')->nullable();
            $table->text('note')->nullable();
            $table->enum('delivery_status', ['pending', 'delivered', 'failed'])->default('pending');
            $table->timestamps();

            $table->index(['delivery_date', 'delivery_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cargo_deliveries');
    }
};
