<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosMemberImportTaskANDPosMemberTaskDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_member_import_task', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
            ;
            
            $table->json('error')->nullable();
            $table->integer('import_cost_time')->default(0);
            $table->integer('execute_cost_time')->default(0);
            $table->timestamp('executed_at')->nullable();
            $table->integer('update_count')->default(0);
            $table->integer('insert_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->timestamps();
        });

        Schema::create('pos_member_import_content', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pos_member_import_task_id')->unsigned()->index();
            $table
                ->foreign('pos_member_import_task_id')
                ->references('id')
                ->on('pos_member_import_task')
                ->onDelete('cascade')
            ;
            
            $table->string('serno')->nullable();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('sernoi')->nullable();
            $table->string('email')->nullable();
            $table->string('cellphone')->nullable();
            $table->string('hometel')->nullable();
            $table->string('officetel')->nullable();
            $table->string('birthday')->nullable();
            $table->string('zipcode')->default('000');
            $table->string('city')->default('台灣省');
            $table->string('state')->default('台灣省');
            $table->string('homeaddress')->nullable();
            $table->string('salepoint_serno')->default('POSCF000000000000000001464');
            $table->string('employee_serno')->default('EMPLY000000000000000001000');
            $table->string('distinction')->nullable();
            $table->string('exploit_serno')->nullable();
            $table->string('exploit_emp_serno')->default('EMPLY000000000000000133950');
            $table->string('member_level_ec')->nullable();
            $table->string('employ_code')->nullable();
            $table->string('category');
            $table->date('period_at')->nullable();
            $table->string('hospital')->nullable();
            $table->text('memo')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->json('flags')->nullable();
            $table->boolean('is_exist')->nullable();
            
            /**
             * 0. 有去到縣市
             * 1. 有在地址中去到區 
             * 2. 有在地址中去到區碼
             * 3. 有透過地址找到符合的區碼
             * 4. 有地址
             * 5. 執行成功/失敗
             */
            $table->integer('status')->unsigned()->default(0); 
            $table->json('error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pos_member_import_content');
        Schema::drop('pos_member_import_task');
    }
}
