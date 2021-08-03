<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserLatLongColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->string('latitude',150)->after('image_url')->nullable();
            $table->string('longitude',150)->after('latitude')->nullable();
            $table->string('forgot_password_hash',100)->after('device')->nullable();
            $table->date('forgot_password_hash_date')->after('forgot_password_hash')->nullable();
            $table->date('token_expiry_at')->after('token');

            $table->smallInteger('vote_cast_count')->after('rating')->default(0);
            $table->date('vote_cast_date')->after('vote_cast_count');
            $table->date('subscription_expiry_date')->after('token_expiry_at');
            $table->bigInteger('vote_total')->after('vote_cast_count')
            ->default(0);
            $table->string('social_id',100)->after('password')->nullable();
            $table->enum('social_type',['facebook','google_plus', 'twitter'])->after('social_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            //
        });
    }
}
