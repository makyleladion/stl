<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'transactions';

    /**
     * Run the migrations.
     * @table transactions
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('The user that made the transaction.');
            $table->unsignedInteger('outlet_id');
            $table->string('transaction_code', 191);
            $table->string('customer_name', 191)->nullable()->default(null);
            $table->tinyInteger('sync')->default('0');
            $table->string('origin', 191)->default('outlet');

            $table->index(["origin"], 'origin');

            $table->index(["user_id"], 'fk_transactions_users1_idx');

            $table->index(["outlet_id"], 'fk_transactions_outlets1_idx');

            $table->unique(["transaction_code"], 'transaction_code_UNIQUE');
            $table->nullableTimestamps();


            $table->foreign('outlet_id', 'fk_transactions_outlets1_idx')
                ->references('id')->on('outlets')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('user_id', 'fk_transactions_users1_idx')
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
