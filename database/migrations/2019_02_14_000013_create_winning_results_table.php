<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinningResultsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'winning_results';

    /**
     * Run the migrations.
     * @table winning_results
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('game', 20);
            $table->string('number', 191);
            $table->date('result_date');
            $table->string('schedule_key', 191);

            $table->index(["result_date"], 'result_date');

            $table->index(["user_id"], 'fk_winning_results_users1_idx');

            $table->index(["schedule_key"], 'schedule_key');

            $table->index(["game"], 'game');
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_winning_results_users1_idx')
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
