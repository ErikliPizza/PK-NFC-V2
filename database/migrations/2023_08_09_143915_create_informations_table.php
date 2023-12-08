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
        Schema::create('informations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('value');
            $table->string('extra')->nullable();
            $table->foreignId('card_id')->references('id')->on('cards')->cascadeOnDelete();
            $table->foreignId('app_id')->references('id')->on('apps')->cascadeOnDelete();
            $table->unsignedSmallInteger('order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informations');
    }
};
