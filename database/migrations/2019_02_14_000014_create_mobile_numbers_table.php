<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileNumbersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'mobile_numbers';

    /**
     * Run the migrations.
     * @table mobile_numbers
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('mobile_number', 13);
            $table->tinyInteger('do_not_send')->default('0');
            $table->string('role', 10);

            $table->index(["user_id"], 'fk_mobile_numbers_users1_idx');

            $table->unique(["mobile_number"], 'mobile_number_UNIQUE');
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_mobile_numbers_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->tableName);
     }
}
