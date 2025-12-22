<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Account\Enums\AccountType;
use Modules\Account\Enums\AccountState;

return new class extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('accounts', function (Blueprint $table) {
            // 1. PRIMARY & IDENTIFICATION
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('account_number', 20)->unique();

            // 2. RELATIONSHIPS
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('parent_account_id')->nullable();

            // 3. ACCOUNT TYPE & STATE
            $table->enum('type', AccountType::values())
                ->default(AccountType::SAVINGS->value);

            $table->enum('state', AccountState::values())
                    ->default(AccountState::ACTIVE->value);

            $table->decimal('balance', 18, 4)->default(0);
            $table->string('currency', 3)->default('USD');

            $table->json('meta')->nullable();

            $table->timestamp('opened_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('parent_account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
