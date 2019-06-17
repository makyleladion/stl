<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'bets';

    /**
     * Run the migrations.
     * @table bets
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('outlet_id');
            $table->unsignedInteger('transaction_id');
            $table->unsignedInteger('ticket_id');
            $table->integer('amount');
            $table->string('type', 191);
            $table->string('number', 191);
            $table->string('game', 20);

            $table->index(["ticket_id"], 'fk_bets_tickets1_idx');

            $table->index(["created_at"], 'created_at');

            $table->index(["outlet_id"], 'fk_bets_outlets1_idx');

            $table->index(["number"], 'number');

            $table->index(["game"], 'game');

            $table->index(["transaction_id"], 'fk_bets_transactions1_idx');

            $table->index(["type"], 'type');
            $table->nullableTimestamps();


            $table->foreign('outlet_id', 'fk_bets_outlets1_idx')
                ->references('id')->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('ticket_id', 'fk_bets_tickets1_idx')
                ->references('id')->on('tickets')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('transaction_id', 'fk_bets_transactions1_idx')
                ->references('id')->on('transactions')
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
