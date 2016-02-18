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
        Schema::table('posmember_import_task_content', function (Blueprint $table) {
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
        Schema::table('posmember_import_task_content', function (Blueprint $table) {
            $table->dropColumn('state_id');
            $table->string('zipcode')->default('000');
            $table->string('city')->default('台灣省');
            $table->string('state')->default('台灣省');
        });
    }
}
