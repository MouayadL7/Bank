<?php

use Modules\Transaction\Enums\TransactionStatusEnum;
use Modules\Transaction\Enums\TransactionTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->onDelete('set null');

            $table->decimal('amount', 18, 4);

            $table->enum('type', TransactionTypeEnum::values());
            $table->enum('status', TransactionStatusEnum::values())->default(TransactionStatusEnum::PENDING->value);

            // Authorization (Chain of Responsibility support)
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            // Scheduled transactions
            $table->boolean('is_scheduled')->default(false);
            $table->timestamp('scheduled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
