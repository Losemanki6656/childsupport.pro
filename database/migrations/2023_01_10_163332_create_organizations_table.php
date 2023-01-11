<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('railway_id')->unsigned()->index()->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->string('name')->nullable();
            $table->string('reception_name')->nullable();
            $table->string('reception_phone')->nullable();
            $table->string('reception_staff')->nullable();
            $table->integer('chat_reception_id')->nullable();
            $table->foreign('railway_id')->references('id')->on('railways');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
