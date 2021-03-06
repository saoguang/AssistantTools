<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersForeignKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Schema::table('users', function ($table){
            $table->foreign('role_id')->references('role_id')->on('t_role');
            $table->foreign('session_id')->references('session_id')->on('t_session');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\Schema::table('users', function ($table){
            $table->dropForeign(['role_id']);
            $table->dropForeign(['session_id']);
        });
    }
}
