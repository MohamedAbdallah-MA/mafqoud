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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password');
            $table->enum('gender',['male' , 'female']);
            $table->string('national_id_front_image');
            $table->string('national_id_back_image');
            $table->string('profile_image');
            $table->string('otp_code')->nullable();
            $table->dateTime('otp_expire_time')->nullable();
            $table->dateTime('phone_verified_at')->nullable();
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')
            ->references('id')
            ->on('locations')
            ->onDelete('CASCADE');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
