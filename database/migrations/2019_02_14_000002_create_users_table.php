<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 191);
            $table->string('email', 191);
            $table->string('password', 191);
            $table->string('status', 10)->default('active');
            $table->tinyInteger('is_admin')->default('0');
            $table->tinyInteger('is_superadmin')->default('0');
            $table->tinyInteger('is_read_only')->default('0');
            $table->tinyInteger('is_usher')->default('0');
            $table->rememberToken();
            $table->tinyInteger('is_password_updated')->default('0');
            $table->tinyInteger('is_betting_enabled')->default('1');
            $table->char('api_token', 60);

            $table->index(["status"], 'status');

            $table->index(["email"], 'email');

            $table->unique(["email"], 'users_email_unique');
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
