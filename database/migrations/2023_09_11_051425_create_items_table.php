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
        Schema::create('items', function (Blueprint $table) {
            $table->id('item'); // Automatically adds an auto-incrementing primary key field `id`.
            $table->unsignedBigInteger('user'); // Use unsignedBigInteger for foreign keys.
            $table->string('name');
            $table->text('description');
            $table->tinyInteger('publish');
            $table->timestamp('expiry');
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
        Schema::dropIfExists('items');
    }
};
