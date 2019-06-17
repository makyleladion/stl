<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutletsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'outlets';

    /**
     * Run the migrations.
     * @table outlets
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('The owner of the outlet');
            $table->integer('user_creator_id')->default('0');
            $table->string('name', 191);
            $table->text('address');
            $table->string('status', 10)->default('active');
            $table->tinyInteger('is_affiliated')->default('0');

            $table->index(["user_id"], 'fk_outlets_users_idx');
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_outlets_users_idx')
                ->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
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
