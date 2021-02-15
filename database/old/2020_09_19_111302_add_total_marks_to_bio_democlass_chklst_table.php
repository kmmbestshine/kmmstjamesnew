<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalMarksToBioDemoclassChklstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bio_democlass_chklst', function (Blueprint $table) {
           $table->integer('total_marks')->after('chklst');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bio_democlass_chklst', function (Blueprint $table) {
            //
        });
    }
}
