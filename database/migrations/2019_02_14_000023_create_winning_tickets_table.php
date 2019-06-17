<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWinningTicketsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'winning_tickets';

    /**
     * Run the migrations.
     * @table winning_tickets
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('winning_result_id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('bet_id');

            $table->index(["bet_id"], 'fk_winning_tickets_bets1_idx');

            $table->index(["ticket_id"], 'fk_winning_tickets_tickets1_idx');

            $table->index(["winning_result_id"], 'fk_winning_tickets_winning_results1_idx');
            $table->nullableTimestamps();


            $table->foreign('bet_id', 'fk_winning_tickets_bets1_idx')
                ->references('id')->on('bets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('ticket_id', 'fk_winning_tickets_tickets1_idx')
                ->references('id')->on('tickets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('winning_result_id', 'fk_winning_tickets_winning_results1_idx')
                ->references('id')->on('winning_results')
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
