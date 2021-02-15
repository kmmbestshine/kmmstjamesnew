<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStudRemarksAndParentImageToHomevisitchcklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('homevisitchcklist', function (Blueprint $table) {
            $table->string('stud_remarks')->nullable()->after('troub_status');
            $table->string('par_image')->nullable()->after('stud_remarks');
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
