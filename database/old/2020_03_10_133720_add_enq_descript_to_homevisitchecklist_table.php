<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEnqDescriptToHomevisitchecklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('homevisitchcklist', function (Blueprint $table) {
           $table->string('enq_descript',300)->after('en_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('homevisitchcklist', function (Blueprint $table) {
            //
        });
    }
}
