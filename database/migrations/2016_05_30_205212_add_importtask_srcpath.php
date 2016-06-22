<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImporttaskSrcpath extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FVSyncQue', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('type_id')->unsigned()->index();            
            $table
                ->foreign('type_id')
                ->references('id')
                ->on('FVSyncType')
                ->onDelete('cascade')
            ;

            $table->tinyInteger('status_code');
            $table->string('dest_file')->nullable();
            $table->integer('select_cost_time')->nullable();
            $table->integer('import_cost_time')->nullable();
            $table->string('last_modified_at')->nullable();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
