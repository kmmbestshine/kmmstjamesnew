<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToDeleStatusFurnitureDistributionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('furniture_distribution_details', function (Blueprint $table) {
            $table->integer('is_delete')->after('comment')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('furniture_distribution_details', function (Blueprint $table) {
            //
        });
    }
}
