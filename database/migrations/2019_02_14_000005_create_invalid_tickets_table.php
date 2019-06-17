<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvalidTicketsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'invalid_tickets';

    /**
     * Run the migrations.
     * @table invalid_tickets
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('outlet_id');
            $table->string('transaction_code', 191)->nullable()->default(null);
            $table->string('ticket_number', 191);
            $table->date('result_date');
            $table->string('schedule_key', 191)->nullable()->default(null);
            $table->integer('amount')->default('0');
            $table->string('error', 191);
            $table->string('source', 191)->default('unknown');

            $table->index(["outlet_id"], 'outlet_id_idx');

            $table->index(["user_id"], 'user_id_idx');

            $table->index(["ticket_number"], 'ticket_number_idx');

            $table->index(["created_at"], 'created_at_idx');
            $table->nullableTimestamps();
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
