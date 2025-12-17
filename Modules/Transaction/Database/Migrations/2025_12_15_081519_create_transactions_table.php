<?php

use App\Modules\Transaction\Enums\TransactionStatus;
use App\Modules\Transaction\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // From account can be nullable (deposit case)
            $table->foreignId('from_account_id')
                ->nullable()
                ->constrained('accounts')
                ->onDelete('cascade');

            $table->foreignId('to_account_id')
                ->constrained('accounts')
                ->onDelete('cascade');

            // Transaction amount
            $table->decimal('amount', 18, 4);

            // Transaction workflow
            $table->enum('type', TransactionType::values());
            $table->enum('status', TransactionStatus::values())
                ->default(TransactionStatus::PENDING->value);

            // Authorization (Chain of Responsibility support)
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users');

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
