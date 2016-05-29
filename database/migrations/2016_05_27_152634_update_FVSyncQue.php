<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFVSyncQue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('FVSyncLog');

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

            $table->string('filepath')->nullable();
            $table->string('filename')->nullable();
            $table->text('error')->nullable();

            $table->string('mdt_time_flag')->nullable();
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
        
    }
}
