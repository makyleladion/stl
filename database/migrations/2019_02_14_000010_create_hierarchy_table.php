<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHierarchyTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'hierarchy';

    /**
     * Run the migrations.
     * @table hierarchy
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_superior_id');
            $table->unsignedInteger('user_subordinate_id');
            $table->string('relationship_label', 45)->nullable()->default(null);

            $table->index(["user_subordinate_id"], 'fk_hierarchy_users2_idx');

            $table->index(["user_superior_id"], 'fk_hierarchy_users1_idx');
            $table->nullableTimestamps();


            $table->foreign('user_superior_id', 'fk_hierarchy_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('user_subordinate_id', 'fk_hierarchy_users2_idx')
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
