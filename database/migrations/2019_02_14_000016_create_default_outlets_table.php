<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultOutletsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'default_outlets';

    /**
     * Run the migrations.
     * @table default_outlets
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('outlet_id');

            $table->index(["outlet_id"], 'fk_default_outlets_outlets1_idx');

            $table->index(["user_id"], 'fk_default_outlets_users1_idx');
            $table->nullableTimestamps();


            $table->foreign('outlet_id', 'fk_default_outlets_outlets1_idx')
                ->references('id')->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_default_outlets_users1_idx')
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
