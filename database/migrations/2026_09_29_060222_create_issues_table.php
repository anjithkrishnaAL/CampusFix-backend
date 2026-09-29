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
           Schema::create('issues', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained()->onDelete('cascade');
       $table->string('category');
       $table->string('title');
       $table->text('description')->nullable();
       $table->string('location_block')->nullable();
       $table->string('location_room')->nullable();
       $table->decimal('latitude', 10, 7)->nullable();
       $table->decimal('longitude', 10, 7)->nullable();
       $table->string('photo_path')->nullable();
       $table->enum('status', ['pending', 'in_progress', 'resolved'])->default('pending');
       $table->timestamps();
   });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
