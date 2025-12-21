<?php

use Modules\Transaction\Enums\TransactionStatus;
use Modules\Transaction\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->onDelete('set null');

            $table->decimal('amount', 18, 4);

            $table->enum('type', TransactionType::values());
            $table->enum('status', TransactionStatus::values())->default(TransactionStatus::PENDING->value);

            // Authorization (Chain of Responsibility support)
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            // Scheduled transactions
            $table->boolean('is_scheduled')->default(false);
            $table->timestamp('scheduled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
