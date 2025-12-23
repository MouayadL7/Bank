<?php

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
    public function up()
    {
        Schema::create('recurrings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('action'); // deposit | withdraw
            $table->decimal('amount', 18, 4);

            $table->uuid('account_uuid');

            $table->string('interval'); // monthly, weekly
            $table->timestamp('next_run_at');

            $table->json('meta')->nullable();

            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recurrings');
    }
};
