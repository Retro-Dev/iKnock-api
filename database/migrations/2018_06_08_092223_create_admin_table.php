<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_group_id')->default(1);
            $table->string('first_name',100);
            $table->string('last_name',100);
            $table->string('email', 150)->unique();
            $table->string('password', 100);
            $table->rememberToken();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('last_login_at');
            $table->string('forgot_password_hash', 100);
            $table->timestamp('forgot_password_hash_created_at')->nullable();
            $table->string('remember_login_token',100);
            $table->timestamp('remember_login_token_created_at')->nullable();
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
        Schema::dropIfExists('admin');
    }
}
