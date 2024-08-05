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

        Schema::create('category_budgets', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 13, 2);
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('transaction_categories')->onDelete('cascade');
            $table->unique(['budget_id', 'category_id']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('category_budgets');
        Schema::enableForeignKeyConstraints();
    }
};
