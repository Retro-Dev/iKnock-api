<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserWishlistRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_wishlist_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('wishlist_id');
            $table->decimal('amount',9, 2);
            $table->decimal('paid_amount',9, 2);
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
        Schema::dropIfExists('user_wishlist_relation');
    }
}
