<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGradeAndConditionToBioSelectedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bio_selected', function (Blueprint $table) {
            $table->string('grade')->after('doj')->nullable();
            $table->longtext('condition')->after('grade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bio_selected', function (Blueprint $table) {
            //
        });
    }
}
