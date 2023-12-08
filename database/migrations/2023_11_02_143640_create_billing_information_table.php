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
        Schema::create('billing_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->string('t_ü')->nullable();
            $table->string('a')->nullable();
            $table->string('t_s_n')->nullable();
            $table->string('v_d')->nullable();
            $table->string('v_n')->nullable();
            $table->string('m_n')->nullable();
            $table->string('email')->nullable();
            $table->string('k_a')->nullable();
            $table->timestamps();
            $table->foreign('card_id')->references('id')->on('cards')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_information');
    }
};
