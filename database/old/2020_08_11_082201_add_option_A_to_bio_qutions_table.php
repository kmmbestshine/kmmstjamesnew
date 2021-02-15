<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionAToBioQutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bio_qutions', function (Blueprint $table) {
            $table->string('option_A')->after('question');
            $table->string('option_B')->after('option_A');
            $table->string('option_C')->after('option_B');
            $table->string('option_D')->after('option_C');
            $table->integer('marks')->after('option_D');
            $table->string('correct_answer')->after('marks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bio_qutions', function (Blueprint $table) {
            //
        });
    }
}
