<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBusIdAndRouteIdToBoardingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boarding', function (Blueprint $table) {
            $table->integer('bus_id')->nullable()->after('session_id');
            $table->integer('route_id')->nullable()->after('route');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boarding', function (Blueprint $table) {
            //
        });
    }
}
