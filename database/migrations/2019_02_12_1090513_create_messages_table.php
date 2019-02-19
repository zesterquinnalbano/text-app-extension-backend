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
            $table->increments('id');
            $table->unsignedInteger('conversation_id');
            $table->unsignedInteger('sent_by')->nullable();
            $table->enum('direction', ['INBOUND', 'OUTBOUND']);
            $table->text('message');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("conversation_id")->references('id')->on('conversations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign("sent_by")->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
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
