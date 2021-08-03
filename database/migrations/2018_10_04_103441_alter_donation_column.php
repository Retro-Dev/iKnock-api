<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDonationColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donation', function (Blueprint $table) {
            $table->integer('user_id')->after('id');
            $table->integer('wishlist_id')->after('user_id')->nullable();
            $table->integer('target_id')->after('wishlist_id')->nullable();
            $table->string('target_type')->after('target_id')->default('artist');
            $table->decimal('amount',9,2)->after('target_type')->default('0');
            $table->decimal('refund_amount',9,2)->after('amount')->default('0');
            $table->string('note')->after('refund_amount');
            $table->date('refund_date')->after('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donation', function (Blueprint $table) {
            //
        });
    }
}
