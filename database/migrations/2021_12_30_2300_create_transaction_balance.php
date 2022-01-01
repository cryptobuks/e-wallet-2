<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionBalance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_balance', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('balance_id');
            $table->enum('transaction_type', ['debit', 'credit']);
            $table->enum('transaction_action', ['topup', 'transfer', 'withdraw', 'payment']);
            $table->decimal('amount');
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
        Schema::dropIfExists('user_balance');
    }
}
