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
        Schema::create('founded_people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('image');
            $table->enum('gender',['male' , 'female']);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('CASCADE');
            $table->unsignedBigInteger('founder_id');
            $table->foreign('founder_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->unsignedBigInteger('police_station_id');
            $table->foreign('police_station_id')->references('id')->on('police_stations')->onDelete('CASCADE');
            $table->date('founded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('founded_people');
    }
};
