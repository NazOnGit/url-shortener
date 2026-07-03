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
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            // Store the link's ID instead of the short code.
            // If the short code changes later, every click still points to the same
            // `links.id`, so Laravel always reads the current short code from the `links` table.
            $table->foreignId('link_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};
