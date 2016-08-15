<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnQueDepend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fvsynctype', function (Blueprint $table) {
            $table->integer('depend_on_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fvsynctype', function (Blueprint $table) {
            $table->dropColumn('depend_on_id');
        });
    }
}
