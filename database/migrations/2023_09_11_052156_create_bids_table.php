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
        Schema::create('bids', function (Blueprint $table) {
            $table->id('bid');
            $table->unsignedBigInteger('bidder'); // Use unsignedBigInteger for foreign keys.
            $table->unsignedBigInteger('item'); // Use unsignedBigInteger for foreign keys.
            $table->integer('amount');
            $table->string('status');
            $table->timestamps();

            // Define the foreign key constraint
            $table->foreign('bidder')->references('id')->on('users');
            $table->foreign('item')->references('item')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
