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
        Schema::table('pos_member_import_task', function (Blueprint $table) {
            $table->string('distinction');
            $table->string('category');
        });

        Schema::table('pos_member_import_content', function (Blueprint $table) {
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
        Schema::table('pos_member_import_task', function (Blueprint $table) {
            $table->dropColumn('distinction');
            $table->dropColumn('category');
        });

        Schema::table('pos_member_import_content', function (Blueprint $table) {
            //$table->string('distinction');
            //$table->string('category');
        });
    }
}
