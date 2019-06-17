<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketCancellationsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'ticket_cancellations';

    /**
     * Run the migrations.
     * @table ticket_cancellations
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('ticket_id');

            $table->index(["ticket_id"], 'fk_ticket_cancellations_tickets1_idx');

            $table->index(["user_id"], 'fk_ticket_cancellations_users1_idx');
            $table->nullableTimestamps();


            $table->foreign('ticket_id', 'fk_ticket_cancellations_tickets1_idx')
                ->references('id')->on('tickets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_id', 'fk_ticket_cancellations_users1_idx')
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
