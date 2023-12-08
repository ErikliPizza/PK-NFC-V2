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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('component')->default('default');
            $table->string('title');
            $table->string('icon')->nullable(); // Store the path to the image file
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('regex')->nullable();
            $table->unsignedSmallInteger('default_order')->nullable();
            $table->foreignId('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
