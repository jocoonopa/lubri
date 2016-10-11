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
        Schema::table('pos_member_import_task', function (Blueprint $table) {
            $table->json('update_flags')->nullable();
            $table->json('insert_flags')->nullable();
            $table->string('memo')->nullable();
        });

        Schema::table('pos_member_import_content', function (Blueprint $table) {
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
        Schema::table('pos_member_import_content', function (Blueprint $table) {
            $table->dropColumn('update_flags');
            $table->dropColumn('insert_flags');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('pushed_at');
        });
    }
}
