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
        Schema::create('missing_person_user', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('missing_person_id');
            $table->foreign('missing_person_id')->references('id')->on('missing_people');

            $table->unsignedBigInteger('searcher_id');
            $table->foreign('searcher_id')->references('id')->on('users');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_person_user');
    }
};
