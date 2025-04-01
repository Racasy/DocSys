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
        Schema::create('document_stamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('debit_account');
            $table->string('credit_account');
            $table->decimal('amount', 12, 2);
            $table->integer('stamp_index')->default(0);
            $table->timestamp('stamped_at')->nullable();
            $table->foreignId('stamped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['document_id', 'stamp_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_stamps');
    }
};
