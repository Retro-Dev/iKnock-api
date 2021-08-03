<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('actor_id');
            $table->integer('target_id');
            $table->integer('reference_id');
            $table->string('reference_module', 50);
            $table->enum('type', ['push', 'email']);
            $table->string('title',100);
            $table->mediumText('description');
            $table->boolean('is_notify');
            $table->boolean('is_read');
            $table->boolean('is_viewed');
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
        Schema::dropIfExists('notification');
    }
}
