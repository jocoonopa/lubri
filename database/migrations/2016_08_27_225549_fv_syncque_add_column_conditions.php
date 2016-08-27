<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FvSyncqueAddColumnConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fvsyncque', function (Blueprint $table) {
            $table->json('conditions')->nullable();
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
            $table->dropColumn('conditions');
        });
    }
}
