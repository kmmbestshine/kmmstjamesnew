<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToHomevisitchcklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('homevisitchcklist', function (Blueprint $table) {
           $table->string('whatsapp_no')->after('teacher_name')->nullable();
           $table->string('fees')->after('whatsapp_no')->nullable();
           $table->string('c3')->after('fees')->nullable();
           $table->string('onlinetest')->after('c3')->nullable();
           $table->string('lkgadm')->after('onlinetest')->nullable();
           $table->string('stdadm')->after('lkgadm')->nullable();
           $table->string('album')->after('stdadm')->nullable();
           $table->string('remarks')->after('album')->nullable();
           $table->string('type')->after('remarks')->nullable();
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
