<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImportContentAndTaskColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posmember_import_task', function (Blueprint $table) {
            $table->json('update_flags')->nullable();
            $table->json('insert_flags')->nullable();
        });

        Schema::table('posmember_import_task_content', function (Blueprint $table) {
            $table->timestamps();
            $table->dateTime('pushed_at')->nullable();
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
            $table->dropColumn('update_flags');
            $table->dropColumn('insert_flags');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('pushed_at');
        });
    }
}
