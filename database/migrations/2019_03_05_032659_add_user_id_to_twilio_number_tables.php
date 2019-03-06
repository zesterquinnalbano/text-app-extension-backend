<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToTwilioNumberTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twilio_numbers', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->after('id');

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twilio_numbers', function (Blueprint $table) {
            $table->dropForeign('twilio_numbers_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
