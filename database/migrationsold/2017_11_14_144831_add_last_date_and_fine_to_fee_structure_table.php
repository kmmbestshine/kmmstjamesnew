<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastDateAndFineToFeeStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fee_structure', function (Blueprint $table) {
            $table->string('last_date')->after('amount')->nullable();
            $table->string('fine')->after('last_date')->nullable();            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fee_structure', function (Blueprint $table) {
            //
        });
    }
}
