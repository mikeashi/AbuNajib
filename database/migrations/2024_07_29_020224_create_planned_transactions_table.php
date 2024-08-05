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
        
        Schema::create('planned_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 13, 2);
            $table->text('description')->nullable();
            $table->string('type');
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('transaction_categories')->onDelete('cascade');
            $table->foreignId('linked_transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('planned_transactions');
        Schema::enableForeignKeyConstraints();
    }
};
