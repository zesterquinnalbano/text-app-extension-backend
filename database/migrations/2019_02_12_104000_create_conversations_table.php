<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('twilio_number_id');
            $table->unsignedInteger('contact_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("twilio_number_id")->references('id')->on('twilio_numbers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign("contact_id")->references('id')->on('contacts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
