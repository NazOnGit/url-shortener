<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * design the columns for the links table, which will store the original URL, the shortened code, and the user ID of the user who created the link. The user_id column is a foreign key that references the id column in the users table. When a user is deleted, all their associated links will also be deleted (cascade on delete).
     */
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            // delete all links associated with a user when the user is deleted
            $table->foreignId('user_id')->constrained()->cascasdeOnDelete();
            $table->string('original_url');
            $table->string('short_code')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
