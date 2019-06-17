<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotnumberSearchesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'hotnumber_searches';

    /**
     * Run the migrations.
     * @table hotnumber_searches
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->date('result_date');
            $table->string('schedule_key', 191)->nullable()->default(null);
            $table->string('keyword', 191);

            $table->index(["user_id"], 'fk_hotnumber_searches_users1_idx');
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_hotnumber_searches_users1_idx')
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
