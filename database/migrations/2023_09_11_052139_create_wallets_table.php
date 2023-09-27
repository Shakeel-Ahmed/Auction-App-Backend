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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id('transaction');
            $table->unsignedBigInteger('user'); // Foreign key referencing the 'id' column in the 'users' table.
            $table->integer('amount');
            $table->integer('credits');
            $table->string('type');
            $table->string('status');
            $table->timestamps();

            // Define the foreign key constraint
            $table->foreign('user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
