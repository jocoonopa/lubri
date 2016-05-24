<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFPMSyncQue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FVSyncType', function (Blueprint $table) {
            $table->increments('id');           
            $table->string('name');
        });

        Schema::create('FVSyncLog', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->unsigned()->index();
            
            $table
                ->foreign('type_id')
                ->references('id')
                ->on('FVSyncType')
                ->onDelete('cascade')
            ;
            $table->string('filepath');
            $table->string('filename');
            $table->string('ip');
            $table->datetime('mrt_time');
            $table->integer('count')->unsigned();
            $table->integer('exec_cost')->unsigned();
            $table->timestamps();
            $table->uuid('uid');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('FVSyncLog');
        Schema::drop('FVSyncType');
    }
}
