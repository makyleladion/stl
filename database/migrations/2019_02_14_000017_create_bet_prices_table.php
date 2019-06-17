<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetPricesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'bet_prices';

    /**
     * Run the migrations.
     * @table bet_prices
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('outlet_id');
            $table->string('type', 191);
            $table->integer('price_per_bet_count');

            $table->index(["outlet_id"], 'fk_bet_prices_outlets1_idx');
            $table->nullableTimestamps();


            $table->foreign('outlet_id', 'fk_bet_prices_outlets1_idx')
                ->references('id')->on('outlets')
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
