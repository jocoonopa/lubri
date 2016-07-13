<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFvsyctypeHname extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fvsynctype', function (Blueprint $table) {
            $table->string('hname');
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
            $table->dropColumn('hname');
        });
    }
}
