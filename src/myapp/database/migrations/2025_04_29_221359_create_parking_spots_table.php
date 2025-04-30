<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parking_spots', function (Blueprint $table) {
            $table->id();
            $table->integer('identifier')->nullable(false)->unique();
            $table->enum('type', ['regular', 'motorcycle']);
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('parking_spots', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
        });

        Schema::dropIfExists('parking_spots');
    }
};
