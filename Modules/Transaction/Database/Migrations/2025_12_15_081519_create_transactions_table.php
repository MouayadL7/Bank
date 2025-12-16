<?php

use App\Modules\Transaction\Enums\TransactionStatus;
use App\Modules\Transaction\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // Migration file for transactions table
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('to_account_id')->constrained('accounts')->onDelete('cascade');
            $table->decimal('balance', 15, 2);
            $table->enum('type', TransactionType::values());
            $table->enum('status', TransactionStatus::values())->default(TransactionStatus::PENDING->value);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
