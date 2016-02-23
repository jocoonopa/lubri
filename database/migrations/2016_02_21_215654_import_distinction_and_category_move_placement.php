<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportDistinctionAndCategoryMovePlacement extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posmember_import_task', function (Blueprint $table) {
            $table->string('distinction');
            $table->string('category');
        });

        Schema::table('posmember_import_task_content', function (Blueprint $table) {
            //$table->dropColumn('distinction');
            //$table->dropColumn('category');
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
            $table->dropColumn('distinction');
            $table->dropColumn('category');
        });

        Schema::table('posmember_import_task_content', function (Blueprint $table) {
            //$table->string('distinction');
            //$table->string('category');
        });
    }
}
