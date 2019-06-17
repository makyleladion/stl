<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'tickets';

    /**
     * Run the migrations.
     * @table tickets
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
            $table->string('ticket_number', 191);
            $table->date('result_date');
            $table->string('schedule_key', 191);
            $table->integer('print_count');
            $table->tinyInteger('is_cancelled')->default('0');

            $table->index(["schedule_key"], 'schedule_key');

            $table->index(["created_at"], 'created_at');

            $table->index(["updated_at"], 'updated_at');

            $table->index(["transaction_id"], 'fk_tickets_transactions1_idx');

            $table->index(["is_cancelled"], 'is_cancelled');

            $table->index(["result_date"], 'result_date');

            $table->index(["outlet_id"], 'fk_tickets_outlets1_idx');

            $table->index(["user_id"], 'fk_tickets_users1_idx');

            $table->unique(["ticket_number"], 'ticket_number_UNIQUE');
            $table->nullableTimestamps();


            $table->foreign('outlet_id', 'fk_tickets_outlets1_idx')
                ->references('id')->on('outlets')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('transaction_id', 'fk_tickets_transactions1_idx')
                ->references('id')->on('transactions')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('user_id', 'fk_tickets_users1_idx')
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
