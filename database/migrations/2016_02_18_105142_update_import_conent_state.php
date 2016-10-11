<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateImportConentState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_member_import_content', function (Blueprint $table) {
            $table->integer('state_id')->unsigned()->index()->nullable();
            $table
                ->foreign('state_id')
                ->references('id')
                ->on('state')
                ->onDelete('cascade')
            ;

            $table->dropColumn('zipcode');
            $table->dropColumn('city');
            $table->dropColumn('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_member_import_content', function (Blueprint $table) {
            $table->dropColumn('state_id');
            $table->string('zipcode')->default('000');
            $table->string('city')->default('台灣省');
            $table->string('state')->default('台灣省');
        });
    }
}
