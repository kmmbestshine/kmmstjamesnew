<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFrfamarksAndTofamarksAndFrsamarksAndTosamarksToGradeSystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grade_system', function (Blueprint $table) {
            $table->string('frfamark')->after('grade')->nullable();
             $table->string('tofamark')->after('frfamark')->nullable();
             $table->string('fagrade')->after('tofamark')->nullable();
             $table->string('frsamark')->after('fagrade')->nullable();
             $table->string('tosamark')->after('frsamark')->nullable();
             $table->string('sagrade')->after('tosamark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grade_system', function (Blueprint $table) {
            //
        });
    }
}
