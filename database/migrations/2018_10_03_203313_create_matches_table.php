<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('home_team_id')->unsigned();
            $table->integer('away_team_id')->unsigned();

            $table->tinyInteger('away_team_score')->unsigned()->nullable();
            $table->tinyInteger('home_team_score')->unsigned()->nullable();

            $table->tinyInteger('week')->unsigned();

            $table->timestamps();


            $table->unique(['home_team_id', 'away_team_id']);
            $table->foreign('home_team_id')->references('id')->on('teams')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('away_team_id')->references('id')->on('teams')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
