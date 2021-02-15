<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBusIdAndRouteIdToSionfeeStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sionfee_structure', function (Blueprint $table) {
            $table->integer('route_id')->nullable()->after('session_id');
            $table->integer('bus_id')->nullable()->after('route_id');
            $table->integer('board_id')->nullable()->after('bus_id');
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
