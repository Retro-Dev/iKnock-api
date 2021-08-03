<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->integer('age')->default(0);
            $table->integer('user_property_id')->nullable();
            $table->integer('user_genre_id')->nullable();
            $table->decimal('rating', 9, 2)->default(0);
            $table->enum('gender',['male', 'female'])->nullable();
            $table->string('email', 150)->nullable();
            $table->string('password', 100)->nullable();

            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();

            $table->text('instrument')->nullable();
            $table->string('website',150)->nullable();
            $table->text('social_media_link')->nullable();

            $table->text('about_me')->nullable();

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
        Schema::dropIfExists('user');
    }
}
