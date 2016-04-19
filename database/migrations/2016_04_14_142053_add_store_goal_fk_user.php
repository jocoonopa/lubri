<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStoreGoalFkUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores_goals', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable();

            $table
                ->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores_goals', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
