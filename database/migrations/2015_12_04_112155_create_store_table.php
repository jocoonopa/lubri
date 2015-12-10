<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_area_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('sn')->unique();
            $table->boolean('is_active');
            $table->softDeletes();
            $table->timestamps();

            $table
                ->foreign('store_area_id')
                ->references('id')->on('stores_areas')
                ->onDelete('cascade')
            ;
        });

        Schema::create('stores_goals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('store_id')->unsigned()->nullable();
            $table->string('new_goal')->nullable();
            $table->string('origin_goal')->nullable();
            $table->string('pl_origin_goal');
            $table->string('pl_new_goal')->nullable();
            $table->date('start_at');
            $table->date('stop_at');
            $table->timestamps();

            $table
                ->foreign('store_id')
                ->references('id')->on('stores')
                ->onDelete('cascade')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stores_goals');
        Schema::drop('stores');
        Schema::drop('stores_areas');
    }
}
