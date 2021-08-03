<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender_id');
            $table->integer('receiver_id')->default(0);
            $table->integer('admin_id')->default(0);
            $table->enum('transaction_type', ['debit', 'credit'])->default('credit');
            $table->enum('transaction_mode', ['default', 'manual'])->default('default');
            $table->enum('transaction_head', ['company donation', 'artist donation', 'signup'])->default('signup');
            $table->decimal('amount',9,2)->default(0);
            $table->enum('gateway_type', ['paypal'])->default('paypal');
            $table->text('gateway_request');
            $table->text('gateway_response');
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
}
