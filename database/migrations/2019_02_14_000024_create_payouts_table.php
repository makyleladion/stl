<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayoutsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'payouts';

    /**
     * Run the migrations.
     * @table payouts
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
            $table->unsignedInteger('transaction_id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('bet_id');
            $table->unsignedInteger('winning_result_id');

            $table->index(["outlet_id"], 'fk_payouts_outlets1_idx');

            $table->index(["bet_id"], 'fk_payouts_bets1_idx');

            $table->index(["created_at"], 'created_at');

            $table->index(["updated_at"], 'updated_at');

            $table->index(["ticket_id"], 'fk_payouts_tickets1_idx');

            $table->index(["user_id"], 'fk_payouts_users1_idx');

            $table->index(["winning_result_id"], 'fk_payouts_winning_results1_idx');

            $table->index(["transaction_id"], 'fk_payouts_transactions1_idx');
            $table->nullableTimestamps();


            $table->foreign('bet_id', 'fk_payouts_bets1_idx')
                ->references('id')->on('bets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('outlet_id', 'fk_payouts_outlets1_idx')
                ->references('id')->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('ticket_id', 'fk_payouts_tickets1_idx')
                ->references('id')->on('tickets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('transaction_id', 'fk_payouts_transactions1_idx')
                ->references('id')->on('transactions')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_payouts_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('winning_result_id', 'fk_payouts_winning_results1_idx')
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
