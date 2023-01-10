<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('railway_id')->unsigned()->index()->nullable();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->bigInteger('result_id')->unsigned()->index()->nullable();
            $table->bigInteger('organization_id')->unsigned()->index()->nullable();
            $table->string('fullname')->nullable();
            $table->string('phone')->nullable();
            $table->integer('chat_id')->nullable();
            $table->text('token')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('status_message')->default(false);
            $table->boolean('status')->default(true);
            $table->text('comment_result')->nullable();
            $table->foreign('railway_id')->references('id')->on('railways');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('result_id')->references('id')->on('results');
            $table->foreign('organization_id')->references('id')->on('organizations');
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
        Schema::dropIfExists('messages');
    }
}
