<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityAndState extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city', function (Blueprint $table) {
            $table->increments('id');
            $table->string('telcode');
            $table->string('name');
            $table->string('pastname');
        });

        Schema::create('state', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('city_id')->unsigned()->index();
            $table
                ->foreign('city_id')
                ->references('id')
                ->on('city')
                ->onDelete('cascade')
            ;

            $table->string('name');
            $table->string('pastname');
            $table->string('zipcode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('city');
        Schema::drop('state');
    }
}
