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
        Schema::disableForeignKeyConstraints();

        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('amount', 13, 2);
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('source_account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('destination_account_id')->constrained('accounts')->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('transfers');
        Schema::enableForeignKeyConstraints();
    }
};
