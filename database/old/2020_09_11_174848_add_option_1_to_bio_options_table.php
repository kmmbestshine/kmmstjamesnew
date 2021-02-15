<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOption1ToBioOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bio_options', function (Blueprint $table) {
           $table->string('option_1')->after('question_id');
            $table->string('option_2')->after('option_1');
            $table->string('option_3')->after('option_2');
            $table->string('option_4')->after('option_3');
            $table->string('correct_option')->after('option_4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bio_options', function (Blueprint $table) {
            //
        });
    }
}
