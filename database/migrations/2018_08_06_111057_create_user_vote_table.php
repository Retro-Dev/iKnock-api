<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id');
            $table->integer('target_id');
            $table->decimal('overall_performance',9, 2);
            $table->decimal('instrumental_performance',9, 2);
            $table->decimal('voice_performance',9, 2);
            $table->decimal('applause',9, 2);
            $table->boolean('is_applicable');
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
        Schema::dropIfExists('user_vote');
    }
}
