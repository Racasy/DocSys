<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // the client who needs to fulfill the request
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->string('status', 50)->default('pending');
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            // Set up the foreign key to users table
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};

