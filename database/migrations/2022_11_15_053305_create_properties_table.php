<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->longText('description');
            $table->string('category')->nullable();
            $table->string('amenities')->nullable();
            $table->string('addresses')->nullable();
            $table->decimal('price', $precision = 8, $scale = 2)->default(0);
            $table->enum('status', ['available','unavailable'])->default('available');
            $table->boolean('verified')->default(0);
            $table->enum('type', ['rent','sale'])->default('rent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
};
