<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPosmembertaskColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_member_import_task', function (Blueprint $table) {
            $table->tinyInteger('status_code'); 
            $table->integer('total_count')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_member_import_task', function (Blueprint $table) {
            $table->dropColumn('status_code');
            $table->dropColumn('total_count');
        });
    }
}
