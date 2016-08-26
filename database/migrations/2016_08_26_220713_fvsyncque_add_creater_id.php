<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FvsyncqueAddCreaterId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fvsyncque', function (Blueprint $table) {
            $table->integer('creater_id')->unsigned()->index()->default(89);

            $table
                ->foreign('creater_id')
                ->references('id')
                ->on('users')
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
        Schema::table('fvsyncque', function (Blueprint $table) {
            $table->dropColumn('creater_id');
        });
    }
}
