<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBusnoAndRouteToSionfeeStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sionfee_structure', function (Blueprint $table) {
        $table->string('bus_no')->nullable()->after('boarding');
        $table->string('bus_route')->nullable()->after('reg_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('sionfee_structure', function (Blueprint $table) {
            //
        });
    }
}
