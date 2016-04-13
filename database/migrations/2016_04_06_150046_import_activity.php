<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_member_import_kind', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('view_path');
            $table->string('observer')->nullable();
            $table->string('pusher')->nullable();
            $table->string('handler')->nullable();
            $table->string('factory')->nullable();
            $table->string('validator')->nullable();

            $table->json('allow_corps');
            $table->boolean('is_enabled');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('pos_member_import_task', function (Blueprint $table) {
            $table->integer('kind_id')->unsigned()->nullable();

            $table
                ->foreign('kind_id')
                ->references('id')->on('pos_member_import_kind')
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
        Schema::table('posmember_import_task', function (Blueprint $table) {
            $table->dropColumn('posmember_import_kind_id');
        });
        Schema::drop('posmember_import_kind');
    }
}
